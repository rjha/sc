<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Feedback {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Feedback';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = " select f.* from sc_feedback f order by f.id desc limit %d " ; 
            $sql = sprintf($sql,$limit);
			$rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		
		}
		
		static function getTotalCount() {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select count(id) as count from sc_feedback ";
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}

		static function getPaged($start,$direction,$limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

            settype($start,"integer");
            settype($limit,"integer");
			$direction = $mysqli->real_escape_string($direction);

            $sql = " select f.* from sc_feedback f " ;

            if($direction == 'after') {
                $sql .= " where  f.id < %d order by f.id DESC LIMIT %d ";

            } else if($direction == 'before'){
                $sql .= " where  f.id > %d order by f.id ASC LIMIT %d ";
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }

            $sql = sprintf($sql,$start,$limit);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }
            
            return $rows;	

		}

	}
}
?>
