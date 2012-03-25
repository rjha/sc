<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Group {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Group';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = "select distinct token from sc_user_group order by id desc LIMIT %d " ; 
            $sql = sprintf($sql,$limit);
			$rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}

         static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");

            $sql = "select ug.token as token from sc_user_group ug where ug.login_id = %d order by token " ;
            $sql = sprintf($sql,$loginId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getCountOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");

            $sql = "select count(id) as count from sc_user_group ug where ug.login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function setFeatureSlug($loginId,$slug) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");
			$slug = $mysqli->real_escape_string($slug);

            //operation needs admin privileges
            $userRow = \com\indigloo\sc\mysql\User::getOnLoginId($loginId);
            if($userRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = "update sc_feature_group set slug = '%s' where id = 1 ";
            $sql = sprintf($sql,$slug);

            $code = MySQL\Connection::ACK_OK;
            MySQL\Helper::executeSQL($mysqli,$sql);
            return $code ;
        }

        static function getFeatureSlug() {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select slug from sc_feature_group where id = 1 " ; 
			$row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
		}


	}
}
?>
