<?php

namespace com\indigloo\sc\mysql {

    use com\indigloo\mysql as MySQL;

    class SelectBox {
        
        static function get($name) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $name = $mysqli->real_escape_string($name);

            $sql = " select * from sc_ui_list where name = '%s' order by ui_order ASC " ;
            $sql = sprintf($sql,$name);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows ;

        }

    }

}
?>
