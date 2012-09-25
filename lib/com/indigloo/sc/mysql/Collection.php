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
            $sql = " insert into sc_set(set_key,set_hash,member,source,created_on)" ;
            $sql .= " values(?,?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss",$key,$hash,$member,$source);
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
            $sql = "delete from sc_set where set_hash = ? and member = ?" ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss",$hash,$member) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function uizmembers($key) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // sanitize input
            $key = $mysqli->real_escape_string($key);

            // convert to BIN(16) for faster lookup
            $sql = " select * from sc_ui_zset where set_hash = unhex(md5('%s')) order by ui_order " ;
            $sql = sprintf($sql,$key);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function uizmemberOnSeoKey($key,$seoKey) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // sanitize input
            $seoKey = $mysqli->real_escape_string($seoKey);
            $sql = " select * from sc_ui_zset where set_hash = unhex(md5('%s')) and seo_key = '%s' " ;
            $sql = sprintf($sql,$key,$seoKey);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function uizmembersAsMap($key){
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select seo_key as id, name as name from sc_ui_zset " ;
            $sql .= " where set_hash = unhex(md5('%s')) order by ui_order" ;
            $sql = sprintf($sql,$key);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        
    }
}
?>
