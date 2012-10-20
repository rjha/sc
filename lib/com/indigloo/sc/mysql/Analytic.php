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

    }
}
?>
