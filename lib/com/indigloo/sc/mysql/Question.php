<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    
    class Question {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Question';

		//DB columns for filters
		const LOGIN_COLUMN = "login_id" ;
		const FEATURE_COLUMN = "is_feature" ;

		static function getOnId($questionId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$questionId = $mysqli->real_escape_string($questionId);
			
            $sql = " select q.*,l.name as user_name from sc_question q,sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id = ".$questionId ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}


         //@see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/ 
         static function getRandom($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$limit = $mysqli->real_escape_string($limit);

            $sql = " SELECT q.*,l.name as user_name FROM sc_question q,sc_login l WHERE q.login_id = l.id " ;
            $sql .=" and RAND()<(SELECT ((%d/COUNT(*))*4) FROM sc_question q2) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$limit,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}

         static function getPosts($filter,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$limit = $mysqli->real_escape_string($limit);
            $sql = "select q.*,l.name as user_name  from sc_question q, sc_login l where q.login_id = l.id ";

            if(Util::tryArrayKey($filter,self::FEATURE_COLUMN)) {
                $value = $mysqli->real_escape_string($filter[self::FEATURE_COLUMN]); 
                $sql .= " and is_feature = ".$value;
            }
            
            $sql .= " order by id desc limit ".$limit ;
             
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}

        static function getOnLoginId($loginId,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);
			$limit = $mysqli->real_escape_string($limit);
			
            $sql = " select q.*,l.name as user_name from sc_question q,sc_login l where q.login_id = l.id " ; 
            $sql .= " and  q.login_id = ".$loginId ." order by id desc limit ".$limit ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}


       	/*
		 *
		 * 1. we need to fetch rows from mysql doing a range scan on ids 
		 * returned by sphinx.
		 *
         * 2. To preserve the order of ids returned by sphinx you need to create a
         * sort field like 
         * $sql .= " ORDER BY FIELD(q.id,".$strIds. ") " ;
		 * @see http://sphinxsearch.com/info/faq/ 
         *
         * 3. we want sorting to be done on our DB created_on column (our choice)
		 *
		 */
		static function getOnSearchIds($strIds) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select q.*,l.name as user_name from sc_question q, sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id in (".$strIds. ") " ;
            $sql .= " ORDER BY q.id desc" ;

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
                               $groupSlug) {
			
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
            $itemId = NULL ;

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
                //update pseudo ID
                $itemId = PseudoId::encode($lastInsertId);
                $sql = " update sc_question set pseudo_id = %d where id = %d " ;
                $sql = sprintf($sql,$itemId,$lastInsertId);
                MySQL\Helper::executeSQL($mysqli,$sql);
                
            }
	
			return array('code' => $code, 'itemId' => $itemId) ;
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

        static function setFeature($loginId,$strIds,$value){
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            //operation needs admin privileges
            $userRow = \com\indigloo\sc\mysql\User::getOnLoginId($loginId);
            if($userRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = " update sc_question set is_feature = ".$value." where ID IN (".$strIds.")" ;
            $code = MySQL\Connection::ACK_OK;
            MySQL\Helper::executeSQL($mysqli,$sql);
            return $code ;
            
		}


	}
}
?>
