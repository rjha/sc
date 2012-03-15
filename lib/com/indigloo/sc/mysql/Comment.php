<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Comment {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Comment';
		//DB columns for filters
		const LOGIN_COLUMN = "login_id" ;

		static function getOnPostId($postId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$postId = $mysqli->real_escape_string($postId);
			
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;
            $sql .= " where l.id = a.login_id and  a.post_id = ".$postId ;
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}
		
		static function getOnId($commentId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$commentId = $mysqli->real_escape_string($commentId);
			
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l ";
            $sql .= " where l.id = a.login_id and a.id = ".$commentId ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}
		
		static function getLatest($count,$dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

			$condition = '' ;
			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {

				$loginId = $mysqli->real_escape_string($dbfilter[self::LOGIN_COLUMN]); 
				$condition = " and a.login_id = ".$loginId;
			}

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;
            $sql .= " where l.id = a.login_id ".$condition." order by id desc LIMIT ".$count ;
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

            $sql = " select count(id) as count from sc_comment ".$condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

		static function getPaged($start,$direction,$count,$dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l where l.id = a.login_id " ;
            $predicate = '' ;
			$condition = '' ;

			if(array_key_exists(self::LOGIN_COLUMN,$dbfilter)) {
				$loginId = $mysqli->real_escape_string($dbfilter[self::LOGIN_COLUMN]); 
				$condition = " and a.login_id = ".$loginId ;
			}

            if($direction == 'after') {
                $predicate = " and a.id < ".$start ;
                $predicate .= $condition ;
                $predicate .= " order by a.id DESC LIMIT " .$count;

            } else if($direction == 'before'){
                $predicate = " and a.id > ".$start ;
                $predicate .= $condition ;
                $predicate .= " order by a.id ASC LIMIT " .$count;
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

        static function create($postId,
								$comment,
								$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_comment(post_id,description,login_id, created_on) " ;
            $sql .= " values(?,?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("isi",
								$postId,
								$comment,
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
		
		static function update($commentId,$comment,$loginId) {
			
			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "update sc_comment set description = ?, updated_on = now() where id = ? and login_id = ?" ;
			
			
			$stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sii",$comment,$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
			
		}

		static function delete($commentId,$loginId) {

			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "delete from sc_comment where id = ? and login_id = ?" ;

			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$commentId,$loginId) ;
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
