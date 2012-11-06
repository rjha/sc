<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    
    class Analytic {

        static function currentSessions() {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select l.id as login_id, l.name, s.updated_on from sc_php_session s, sc_login l " ;
            $sql .= "where l.session_id = s.session_id and s.updated_on > (now() - interval 1 day) " ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getSiteCounters() {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select * from sc_site_counter " ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getUserCounters($loginId) {
            settype($loginId,"integer");

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select * from sc_user_counter where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

    }
}
?>
