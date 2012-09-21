<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Collection {

        static function sadd($key,$member,$source) {
            //hash of key
            $hash = md5(trim($key),TRUE);
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_set_member(set_hash,member,source,created_on)" ;
            $sql .= " values(?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss",$hash,$member,$source);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function srem($key,$member) {
            //hash of key
            $hash = md5(trim($key),TRUE);
            $member = trim($member);
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_set_member where set_hash = ? and member = ?" ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss",$hash,$member) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }
        
    }
}
?>
