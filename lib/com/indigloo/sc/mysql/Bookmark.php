<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Bookmark {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Bookmark';

        static function getOnLoginId($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");
            $sql = " select * from sc_user_bookmark where login_id = %d ";
            $sql = sprintf($sql,$loginId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function add($loginId,$postId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_user_bookmark(login_id,post_id, created_on) " ;
            $sql .= " values(?,?,now()) ";

            $code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ii", $loginId, $postId);
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
	}
}
?>
