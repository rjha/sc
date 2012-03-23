<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Twitter {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Twitter';

		static function getOnTwitterId($twitterId) {
            //@todo column length checks
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$twitterId = $mysqli->real_escape_string($twitterId);

			$sql = " select * from sc_twitter where twitter_id = '".$twitterId."' " ;
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}

		static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);

			$sql = " select * from sc_twitter where login_id = ".$loginId ;
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}


		static function create($twitterId,$name,$screenName,$location,$image,$loginId){
            //@todo column length checks
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$twitterId = $mysqli->real_escape_string($twitterId);
			$loginId = $mysqli->real_escape_string($loginId);

			$sql = " insert into sc_twitter(twitter_id,name,screen_name,location,profile_image,login_id,created_on) " ;
			$sql .= " values(?,?,?,?,?,?,now()) ";

			$code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssssi",
                        $twitterId,
                        $name,
                        $screenName,
                        $location,
						$image,
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
