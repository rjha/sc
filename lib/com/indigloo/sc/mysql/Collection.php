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

        /*
         * why use values() function inside sql statmenet?
         * http://dev.mysql.com/doc/refman/5.0/en/miscellaneous-functions.html#function_values
         * 
         * from the manual 
         * -------------------------------------------------------------------------------
         * you can use the VALUES(col_name) function in the UPDATE clause to refer to 
         * column values from the INSERT portion of the statement. In other words, 
         * VALUES(col_name) in the UPDATE clause refers to the value of col_name that would 
         * be inserted, had no duplicate-key conflict occurred.
         * -------------------------------------------------------------------------------
         * 
         */
        static function glset($key,$value) {
          
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //create the key if it does not exist
            // otherwise update
            $sql = " insert into sc_glob_table(t_key,t_hash,t_value,created_on) values (?,?,?,now()) " ;
            $sql .= " on duplicate key update t_value = values(t_value), updated_on = values(created_on) " ;
            
            $stmt = $mysqli->prepare($sql);
            $khash = md5(trim($key),TRUE);
            if ($stmt) {
                $stmt->bind_param("sss",$key,$khash,$value);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function glget($key) {

            $khash = md5(trim($key),TRUE);

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select t_value from sc_glob_table where t_hash = '%s'" ;
            $sql = sprintf($sql,$khash);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        
    }
}
?>
