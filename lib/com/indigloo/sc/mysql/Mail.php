<?php

namespace com\indigloo\sc\mysql {

    use com\indigloo\mysql as MySQL;

    class Mail {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Mail';
        
        static function addResetPassword($name,$email,$token) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $name = $mysqli->real_escape_string($name);
            $email = $mysqli->real_escape_string($email);
            $token = $mysqli->real_escape_string($token);


            $code = MySQL\Connection::ACK_OK;
            
            $sql = " insert into sc_reset_password(name,email,token,created_on,expired_on) " ;
            $sql .= " values(?,?,?,now(), now()+INTERVAL 1 DAY) ";

            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sss",$name,$email,$token);
                $stmt->execute();
                if ($mysqli->affected_rows != 1) {
                    $dbCode = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }

                $stmt->close();
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
            
            return $code;
        }

    }

}
?>
