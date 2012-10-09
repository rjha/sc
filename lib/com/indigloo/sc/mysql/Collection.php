<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Collection {

        static function sadd($key,$member) {

            //hash of key and member
            $khash = md5(trim($key),TRUE);
            $mhash =  md5(trim($member),TRUE);

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_set(set_key,set_hash,member,member_hash,created_on)" ;
            $sql .= " values(?,?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss",$key,$khash,$member,$mhash);
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
            //convert into bin(16)
            $khash = md5(trim($key),TRUE);
            $mhash = md5(trim($member),TRUE);

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_set where set_hash = ? and member_hash = ?" ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss",$khash,$mhash) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function smembers($key) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // sanitize input
            $key = $mysqli->real_escape_string($key);
            $khash = md5(trim($key),TRUE);

            // convert to BIN(16) for faster lookup
            $sql = " select * from sc_set where set_hash = '%s' " ;
            $sql = sprintf($sql,$khash);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function uizmembers($key) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // sanitize input
            $key = $mysqli->real_escape_string($key);
            $khash = md5(trim($key),TRUE);

            // convert to BIN(16) for faster lookup
            $sql = " select * from sc_ui_zset where set_hash = '%s' order by ui_order " ;
            $sql = sprintf($sql,$khash);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function uizmemberOnSeoKey($key,$seoKey) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // sanitize input
            $seoKey = $mysqli->real_escape_string($seoKey);
            $khash = md5(trim($key),TRUE);

            $sql = " select * from sc_ui_zset where set_hash = '%s' and seo_key = '%s' " ;
            $sql = sprintf($sql,$khash,$seoKey);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function uizmembersAsMap($key){
            $khash = md5(trim($key),TRUE);

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select seo_key as id, name as name from sc_ui_zset " ;
            $sql .= " where set_hash = '%s' order by ui_order" ;
            $sql = sprintf($sql,$khash);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        
    }
}
?>
