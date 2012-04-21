<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants ;
    
    class Group {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Group';

		static function getLatest($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = "select token from sc_group_master order by id desc LIMIT %d " ; 
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

        static function process($postId,$loginId,$version,$catCode,$group_slug) {

            settype($postId,"integer");
            settype($loginId,"integer");
            settype($version,"integer");

            $sqlm1 = "insert ignore into sc_group_master(token,cat_code,created_on) values('%s','%s',now()) ";
            $sqlm2 = "insert ignore into sc_user_group(login_id,token,created_on) values('%d','%s',now()) ";
            $sqlm3 = "update sc_site_tracker set group_flag = 1 where post_id = %d and version = %d " ;

            try {

                $host = Config::getInstance()->get_value("mysql.host");
                $dbname = Config::getInstance()->get_value("mysql.database");
                $dsn = sprintf("mysql:host=%s;dbname=%s",$host,$dbname);

                $user = Config::getInstance()->get_value("mysql.user");
                $password = Config::getInstance()->get_value("mysql.password");
                $dbh = new \PDO($dsn, $user, $password);

                //throw exceptions
                $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                //Tx start
                $dbh->beginTransaction();

                $slugs = explode(Constants::SPACE,$group_slug);
                foreach($slugs as $slug){
                    if(Util::tryEmpty($slug)) continue;
                    //do processing
                    $sql = sprintf($sqlm1,$slug,$catCode);
                    $dbh->exec($sql);
                    $sql = sprintf($sqlm2,$loginId,$slug);
                    $dbh->exec($sql);

                }

                //All group slugs for post processed
                $sql = sprintf($sqlm3,$postId,$version);
                $dbh->exec($sql);

                //Tx end
                $dbh->commit();
                $dbh = null;
            } catch (PDOException $e) {
                $dbh->rollBack();
                trigger_error($e->getMessage(),E_USER_ERROR);
            }

        }

	}
}
?>
