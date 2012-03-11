<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    
    class Question {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Question';

		//DB columns for filters
		const LOGIN_COLUMN = "login_id" ;

		static function getOnId($questionId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$questionId = $mysqli->real_escape_string($questionId);
			
            $sql = " select q.*,l.name as user_name from sc_question q,sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id = ".$questionId ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

         static function getRandom($start,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$start = $mysqli->real_escape_string($start);
			$limit = $mysqli->real_escape_string($limit);
			
            $sql = " select q.* from sc_question q limit {start},{limit} " ;
            $sql = str_replace(array("{start}","{limit}"), array(0 => $start, 1=> $limit), $sql); 

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}

        static function getOnLoginId($loginId,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);
			$limit = $mysqli->real_escape_string($limit);
			
            $sql = " select q.* from sc_question q where q.login_id = ".$loginId ;
            $sql .=  " order by id desc limit ".$limit ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}


       	/*
		 *
		 * 1. we need to fetch rows from mysql doing a range scan on ids 
		 * returned by sphinx.
		 *
		 * 2. we have to reorder the results returned by mysql because sphinx
		 * results have a different order (e.g relevance) 
		 * @see http://sphinxsearch.com/info/faq/ 
		 *
		 * @todo - fix - this order by clause causes a FTS
		 *
		 */
		static function getOnSearchIds($strIds) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select q.*,l.name as user_name from sc_question q, sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id in (".$strIds. ") " ;
            $sql .= " ORDER BY FIELD(q.id,".$strIds. ") " ;
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}
		
		static function getLatest($count,$dbfilter) {
			
			$mysqli = MySQL\Connection::getInstance()->getHandle();

			$condition = '' ;
			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
				$loginId = $mysqli->real_escape_string($dbfilter[self::LOGIN_COLUMN]);
				$condition = " and q.login_id = ".$loginId;
			}

            $sql = " select q.*,l.name as user_name from sc_question q,sc_login l " ;
            $sql .= " where l.id=q.login_id ".$condition." order by q.id desc LIMIT ".$count ;
			
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
			
		}

		static function getTotalCount($dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

			$condition = '';
			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
				$loginId = $mysqli->real_escape_string($dbfilter[self::LOGIN_COLUMN]);
				$condition = " where login_id = ".$loginId;
			}

            $sql = " select count(id) as count from sc_question ".$condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

		}

		static function getPaged($start,$direction,$count,$dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            
            // primary key id is an excellent proxy for created_on column
            // latest posts has max(id) and appears on top
            // so AFTER (NEXT) means id < latest post id
            
            $sql = " select q.*,l.name as user_name from sc_question q,sc_login l  where l.id = q.login_id " ;
            $predicate = '' ;
			$condition = '' ;

			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
				$loginId = $mysqli->real_escape_string($dbfilter[self::LOGIN_COLUMN]);
				$condition = " and q.login_id = ".$loginId ;
			}

            if($direction == 'after') {
                $predicate = " and q.id < ".$start ;
                $predicate .= $condition ;
                $predicate .= " order by q.id DESC LIMIT " .$count;

            } else if($direction == 'before'){
                $predicate = " and q.id > ".$start ;
                $predicate .= $condition ;
                $predicate .= " order by q.id ASC LIMIT " .$count;
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }
            
            $sql .= $predicate ;
            
            if(Config::getInstance()->is_debug()) {
                Logger::getInstance()->debug("sql => $sql \n");
            }
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }
            
            return $rows;	

		}

		static function update($questionId,
						       $title,
                               $description,
                               $location,
                               $tags,
                               $linksJson,
							   $imagesJson,
                               $loginId,
                               $groupSlug)
		
		{
			
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_question set title=?,description=?,location = ?,tags =?,links_json =?, " ;
			$sql .= " images_json=?,group_slug = ? where id = ? and login_id = ?" ;
			
			
            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            
            if ($stmt) {
                $stmt->bind_param("sssssssii",
                        $title,
                        $description,
                        $location,
                        $tags,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
						$questionId,
						$loginId);
                
                      
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    $code = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }
                $stmt->close();
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
            
            return $code ;
            
		}
		
        static function create($title,
                               $description,
                               $location,
                               $tags,
							   $loginId,
                               $linksJson,
                               $imagesJson,
                               $groupSlug) {

			
			
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_question(title,description,location,tags,login_id,links_json, " ;
            $sql .= "images_json,group_slug,created_on) ";
            $sql .= " values(?,?,?,?,?,?,?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
			$lastInsertId = NULL;

            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ssssisss",
                        $title,
                        $description,
                        $location,
						$tags,
						$loginId,
                        $linksJson,
                        $imagesJson,
                        $groupSlug);
                
                      
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    $code = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }
                $stmt->close();

            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			if($code == MySQL\Connection::ACK_OK) {     
                $lastInsertId = MySQL\Connection::getInstance()->getLastInsertId();
            }
	
			return array('code' => $code, 'lastInsertId' => $lastInsertId) ;
        }

		static function delete($questionId,$loginId) {

			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = " delete from sc_question where id = ? and login_id = ?" ;

			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$questionId,$loginId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
		}

	}
}
?>
