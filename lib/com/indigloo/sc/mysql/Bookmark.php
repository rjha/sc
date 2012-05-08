<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Bookmark {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Bookmark';

        static function find($loginId,$itemId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($loginId,"integer");
            settype($itemId,"integer");
            settype($action,"integer");

            $sql = " select count(id) as count from sc_user_bookmark " ;
            $sql .= " where login_id = %d and item_id = %d and action = %d ";
            $sql = sprintf($sql,$loginId,$itemId,$action);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnLoginId($loginId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");
            
            $sql = " select q.* , l.name as user_name " ;
            $sql .= " from sc_post q, sc_user_bookmark b, sc_login l " ;
            $sql .= " where q.pseudo_id = b.item_id and q.login_id = l.id ";
            $sql .= " and b.login_id = %d and b.action = %d limit 20 ";
            
            $sql = sprintf($sql,$loginId,$action);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function add($loginId,$itemId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_user_bookmark(login_id,item_id,action,created_on) " ;
            $sql .= " values(?,?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("iii", $loginId, $itemId,$action);
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
	
		static function delete($bookmarkId) {
			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "delete from sc_user_bookmark where id = ? " ;
			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$bookmarkId) ;
                $stmt->execute();
                $stmt->close();
				
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
			
			return $code ;
		}
        
        static function unfavorite($loginId,$itemId) {
            //remove a save(favorite)- code 2
			$code = MySQL\Connection::ACK_OK ;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "delete from sc_user_bookmark where login_id = ? and item_id = ? and action = 2 " ;
			$stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$loginId,$itemId) ;
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