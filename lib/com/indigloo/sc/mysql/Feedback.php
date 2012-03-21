<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Feedback {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Feedback';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $limit = $mysqli->real_escape_string($limit); 
            $sql = " select f.* from sc_feedback f order by f.id desc limit ".$limit ;
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
            $sql = " select f.* from sc_feedback f " ;

            if($direction == 'after') {
                $sql .= " where  f.id < ".$start ;
                $sql .= " order by f.id DESC LIMIT " .$limit;

            } else if($direction == 'before'){
                $sql .= " where f.id > ".$start ;
                $sql .= " order by f.id ASC LIMIT " .$limit;
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }
            
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
