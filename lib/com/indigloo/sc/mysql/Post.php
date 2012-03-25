<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    
    class Post {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Post';

		//DB columns for filters
		const LOGIN_COLUMN = "login_id" ;
		const FEATURE_COLUMN = "is_feature" ;
		const DATE_COLUMN = "created_on" ;

		static function getOnId($postId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($postId,"integer");
			
            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id = %d " ;
            $sql = sprintf($sql,$postId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}


         //@see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/ 
         static function getRandom($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = " SELECT q.*,l.name as user_name FROM sc_post q,sc_login l WHERE q.login_id = l.id " ;
            $sql .=" and RAND()<(SELECT ((%d/COUNT(*))*4) FROM sc_post q2) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$limit,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}

         static function getPosts($filter,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");
            $sql = "select q.*,l.name as user_name  from sc_post q, sc_login l where q.login_id = l.id ";

            if(Util::tryArrayKey($filter,self::FEATURE_COLUMN)) {
                $value = $filter[self::FEATURE_COLUMN]; 
                settype($value,"integer");
                $sql .= " and is_feature = ".$value;
            }
            
            $sql .= " order by id desc limit %d " ;
            $sql = sprintf($sql,$limit);
             
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

		}

        static function getOnLoginId($loginId,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($limit,"integer");
            settype($loginId,"integer");
			
            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l where q.login_id = l.id " ; 
            $sql .= " and  q.login_id = %d order by id desc limit %d " ;
            $sql = sprintf($sql,$limit);

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
            $strIds = $mysqli->real_escape_string($strIds);

            $sql = " select q.*,l.name as user_name from sc_post q, sc_login l " ;
            $sql .= " where l.id = q.login_id and q.id in (".$strIds. ") " ;
            $sql .= " ORDER BY q.id desc" ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}
		
		static function getLatest($limit,$dbfilter) {
			
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

			$condition = '' ;
			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
                $loginId = $dbfilter[self::LOGIN_COLUMN]; 
                settype($loginId,"integer");
				$condition = " and q.login_id = ".$loginId;
			}

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;
            $sql .= " where l.id=q.login_id ".$condition." order by q.id desc LIMIT ".$limit ;
			
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
			
		}

		static function getTotalCount($dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

			$condition = '';
			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
                $loginId = $dbfilter[self::LOGIN_COLUMN]; 
                settype($loginId,"integer");
				$condition = " where login_id = ".$loginId;
			}

            if(array_key_exists(self::DATE_COLUMN,$dbfilter)) {
				$condition = " where created_on > (now() - interval 24 HOUR) ";
			}


            $sql = " select count(id) as count from sc_post ".$condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

		}

		static function getPaged($start,$direction,$limit,$dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            
            settype($start,"integer");
            settype($limit,"integer");
            // primary key id is an excellent proxy for created_on column
            // latest posts has max(id) and appears on top
            // so AFTER (NEXT) means id < latest post id
            
            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l  where l.id = q.login_id " ;

			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
				$loginId = $dbfilter[self::LOGIN_COLUMN];
                settype($loginId,"integer");
				$sql .= " and q.login_id = ".$loginId ;
			}

            if($direction == 'after') {
                $sql .= " and q.id < %d order by q.id DESC LIMIT %d " ;

            } else if($direction == 'before'){
                $sql .= " and q.id > %d order by q.id ASC LIMIT %d " ;
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }
            
            $sql = sprintf($sql,$start,$limit);
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

		static function update($postId,
						       $title,
                               $description,
                               $linksJson,
							   $imagesJson,
                               $loginId,
                               $groupSlug,
                               $categoryCode) {
			
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_post set title=?,description=?, links_json =?,images_json= ?, " ;
			$sql .= " group_slug = ? , updated_on = now(),cat_code = ? where id = ? and login_id = ?" ;
			
			
            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            
            if ($stmt) {
                $stmt->bind_param("ssssssii",
                        $title,
                        $description,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
                        $categoryCode,
						$postId,
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
							   $loginId,
                               $linksJson,
                               $imagesJson,
                               $groupSlug,
                               $categoryCode) {

			
			
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_post(title,description,login_id,links_json, " ;
            $sql .= "images_json,group_slug,cat_code,created_on) ";
            $sql .= " values(?,?,?,?,?,?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
			$lastInsertId = NULL;
            $itemId = NULL ;

            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ssissss",
                        $title,
                        $description,
						$loginId,
                        $linksJson,
                        $imagesJson,
                        $groupSlug,
                        $categoryCode);
                
                      
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
                $sql = " update sc_post set pseudo_id = %d where id = %d " ;
                $sql = sprintf($sql,$itemId,$lastInsertId);
                MySQL\Helper::executeSQL($mysqli,$sql);
                
            }
	
			return array('code' => $code, 'itemId' => $itemId) ;
        }

		static function delete($postId,$loginId) {

			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = " delete from sc_post where id = ? and login_id = ?" ;

			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$postId,$loginId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
		}

        static function setFeature($loginId,$strIds,$value){
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            $strIds = $mysqli->real_escape_string($strIds);
            settype($loginId,"integer");
            settype($value,"integer");

            //operation needs admin privileges
            $userRow = \com\indigloo\sc\mysql\User::getOnLoginId($loginId);
            if($userRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = " update sc_post set is_feature = %d where ID IN (%s)" ;
            $sql = sprintf($sql,$value,$strIds);

            $code = MySQL\Connection::ACK_OK;
            MySQL\Helper::executeSQL($mysqli,$sql);
            return $code ;
            
		}


	}
}
?>
