<?php
namespace com\indigloo\app\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    class Login {

        static function updateTokenIp($sessionId,$loginId, $access_token, $expires,$remoteIp) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update app_login set access_token = ? , expire_on = %s, " ;
            $sql .= " ip_address = ?, session_id = ? , updated_on = now() where id = ? " ;
            $expiresOn = "(now() + interval ".$expires. " second)";
            $sql = sprintf($sql,$expiresOn);
            
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssi",$access_token,$remoteIp,$sessionId,$loginId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

    }
}

?>
