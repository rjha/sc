<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Facebook {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Facebook';

		static function getOnFacebookId($facebookId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$facebookId = $mysqli->real_escape_string($facebookId);
			$sql = " select * from sc_facebook where facebook_id = ".$facebookId ;
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}

		static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);
			$sql = " select * from sc_facebook where login_id = ".$loginId ;
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}


		static function create($facebookId,$name,$firstName,$lastName,$link,$gender,$email,$loginId){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = " insert into sc_facebook(facebook_id,name,first_name,last_name,link,gender," ;
			$sql .= " email,login_id,created_on) " ;
			$sql .= " values(?,?,?,?,?,?,?,?,now()) ";

			$code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("issssssi",
                        $facebookId,
                        $name,
                        $firstName,
                        $lastName,
                        $link,
						$gender,
						$email,
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

	}
}

?>
