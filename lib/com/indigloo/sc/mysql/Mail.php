<?php

namespace com\indigloo\sc\mysql {

    use com\indigloo\mysql as MySQL;

    class Mail {
        
        static function getResetPassword($email,$token) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $email = $mysqli->real_escape_string($email);
            $token = $mysqli->real_escape_string($token);

            $sql = " select count(id) as count from sc_reset_password where email = '%s' " ;
            $sql .= " and token = '%s' and (now() < expired_on )  ";
            $sql = sprintf($sql,$email,$token);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function getResetPasswordInRange($email) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $email = $mysqli->real_escape_string($email);

            $sql = " select count(id) as count from sc_reset_password where email = '%s' " ;
            $sql .= " and (created_on > now() - INTERVAL 20 MINUTE) ";
            $sql = sprintf($sql,$email);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function addResetPassword($name,$email,$token) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $name = $mysqli->real_escape_string($name);
            $email = $mysqli->real_escape_string($email);
            $token = $mysqli->real_escape_string($token);

            $sql = " insert into sc_reset_password(name,email,token,created_on,expired_on) " ;
            $sql .= " values(?,?,?,now(), now()+INTERVAL 1 DAY) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss",$name,$email,$token);
                $stmt->execute();
                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function flipResetPassword($email) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $email = $mysqli->real_escape_string($email);

            $sql = " update sc_reset_password set flag = 1 where email = '%s' ";
            $sql = sprintf($sql,$email);
            MySQL\Helper::executeSQL($mysqli,$sql);
        }

    }

}
?>
