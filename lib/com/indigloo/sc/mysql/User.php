<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    
    class User {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\User';

		static function getOnId($userId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($userId,"integer");
			
            $sql = " select * from sc_denorm_user where id = %d " ;
            $sql = sprintf($sql,$userId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

        static function getOnEmail($email) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $email = $mysqli->real_escape_string($email);
			
            $sql = " select * from sc_denorm_user where email = '%s' " ;
            $sql = sprintf($sql,$email);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

		static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");
			
            $sql = " select * from sc_denorm_user where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

		static function update($loginId,$firstName,$lastName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl ) {
			$code = MySQL\Connection::ACK_OK;

            $userName = $firstName. ' '.$lastName;
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_denorm_user set first_name= ?, last_name= ?, name= ?, nick_name= ?, " ;
            $sql .= " email= ?,website = ? , blog = ?, location = ?,age=?, photo_url=? where login_id= ? " ;
			
			$stmt = $mysqli->prepare($sql);
        
			if($stmt) {
				$stmt->bind_param("ssssssssisi",$firstName,$lastName,$userName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl,$loginId);
				$stmt->execute();
				$stmt->close();
				
			} else {
				$code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
			}

			return $code ;
		}

		static function addFeedback($feedback) {
			$code = MySQL\Connection::ACK_OK;

			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = " insert into sc_feedback(feedback,created_on) values(?,now()) " ;
			
			$stmt = $mysqli->prepare($sql);
			if($stmt) { 
				$stmt->bind_param("s",$feedback);
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
	}
}
?>
