<?php

namespace com\indigloo\sc\mysql {

    use com\indigloo\mysql as MySQL;

    class SelectBox {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\List';
        
        static function get($name) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $name = $mysqli->real_escape_string($name);
            $sql = " select * from sc_list where name = '".$name. "' order by ui_order ASC " ;
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows ;
            
        }
        
    }

}
?>
