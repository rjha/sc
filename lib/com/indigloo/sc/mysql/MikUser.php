<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;

    class MikUser {
        
        static function getOnLoginId($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");

            $sql = " select * from sc_user where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }
    }
}
?>
