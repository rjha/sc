<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Group {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Group';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select token from sc_user_group order by id desc LIMIT ".$limit ; 
			$rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}

         static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);
            $sql = "select ug.token as token from sc_user_group ug where ug.login_id = ".$loginId ;
            $sql .= " order by token " ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getCountOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);
            $sql = "select count(id) as count from sc_user_group ug where ug.login_id = ".$loginId ;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

	}
}
?>
