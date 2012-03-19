<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    
    class Category {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Category';


        static function getIdNameMap(){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select ui_order as id, display as name from sc_list where name = 'CATEGORY' order by ui_order" ;
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getCodeOnId($categoryId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $categoryId = $mysqli->real_escape_string($categoryId);
            $sql = "select code from sc_list where name = 'CATEGORY' and ui_order = ".$categoryId ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;


        }

		static function getName($code) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $code = $mysqli->real_escape_string($code);

            $sql = "select display as name from sc_list where name = 'CATEGORY' and code ='".$code. "' " ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

		static function getLatest($code,$limit) {
			
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $code = $mysqli->real_escape_string($code);

            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l " ;
            $sql .= " where l.id=q.login_id  and q.cat_code = '".$code."' order by q.id desc LIMIT ".$limit ;
			
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
			
		}

		static function getTotalCount($code) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $code = $mysqli->real_escape_string($code);

            $sql = " select count(id) as count from sc_post where cat_code ='".$code."' " ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

		}

		static function getPaged($start,$direction,$limit,$code) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $code = $mysqli->real_escape_string($code);
            
            $sql = " select q.*,l.name as user_name from sc_post q,sc_login l  where l.id = q.login_id " ;
			$sql .= " where cat_code ='".$code."' " ;
            $predicate = '' ;

            if($direction == 'after') {
                $predicate = " and q.id < ".$start ;
                $predicate .= " order by q.id DESC LIMIT " .$limit;

            } else if($direction == 'before'){
                $predicate = " and q.id > ".$start ;
                $predicate .= " order by q.id ASC LIMIT " .$limit;
            } else {
                trigger_error("Unknow sort direction in query", E_USER_ERROR);
            }
            
            $sql .= $predicate ;
            
            if(Config::getInstance()->is_debug()) {
                Logger::getInstance()->debug("sql => $sql \n");
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
