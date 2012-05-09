<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants ;
    
    use \com\indigloo\sc\util\PDOWrapper ;
    use \com\indigloo\exception\DBException;
    
    class Group {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Group';
        const TOKEN_COLUMN = "token" ;

		static function getLatest($limit,$filters) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = "select g.* from sc_group_master g " ;

            $q = new Query();
            $q->setAlias("com\indigloo\sc\model\Group","g");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;
            $sql .= " order by g.id desc LIMIT %d " ; 
            $sql = sprintf($sql,$limit);

			$rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
		}

        static function getPaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            
            settype($start,"integer");
            settype($limit,"integer");
            $sql = "select g.* from sc_group_master g " ;

            $q = new Query();
            $q->setAlias("com\indigloo\sc\model\Group","g");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;

            if($direction == 'after') {
                $sql .= " and g.id < %d order by g.id DESC LIMIT %d " ;

            } else if($direction == 'before'){
                $sql .= " and g.id > %d order by g.id ASC LIMIT %d " ;
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

        static function getTotalCount($filters){
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select count(g.id) as count from sc_group_master g " ;

            $q = new Query();
            $q->setAlias("com\indigloo\sc\model\Group","g");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }
       
        static function getRandom($limit) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit,"integer");

            $sql = " SELECT g.*  FROM sc_group_master g where " ;
            $sql .=" RAND()<(SELECT ((%d/COUNT(*))*4) FROM sc_group_master g2) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$limit,$limit);

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

            $sqlm1 = "insert ignore into sc_group_master(token,name,cat_code,created_on) values('%s','%s','%s',now()) ";
            $sqlm2 = "insert ignore into sc_user_group(login_id,token,created_on) values('%d','%s',now()) ";
            $sqlm3 = "update sc_site_tracker set group_flag = 1 where post_id = %d and version = %d " ;

            try {
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $slugs = explode(Constants::SPACE,$group_slug);
                foreach($slugs as $slug){
                    if(Util::tryEmpty($slug)) continue;
                    //do processing
                    $name = \com\indigloo\util\StringUtil::convertKeyToName($slug);
                    $sql = sprintf($sqlm1,$slug,$name,$catCode);
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
