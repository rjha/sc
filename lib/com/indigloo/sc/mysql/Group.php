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

        static function setFeature($loginId,$slug) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);

            //operation needs admin privileges
            $userRow = \com\indigloo\sc\mysql\User::getOnLoginId($loginId);
            if($userRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = "update sc_feature_group set slug = '".$slug."' where id = 1 ";
            $code = MySQL\Connection::ACK_OK;
            MySQL\Helper::executeSQL($mysqli,$sql);
            return $code ;
        }

        static function getFeature() {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select slug from sc_feature_group where id = 1 " ; 
			$row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}


	}
}
?>
