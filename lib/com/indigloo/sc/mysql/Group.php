<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;

    class Group {
        
        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = "select g.* from sc_group_master g " ;

            $q = new MySQL\Query($mysqli);
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

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = "select g.* from sc_group_master g" ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Group","g");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction,"g.id",$limit);

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

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Group","g");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getRandom($limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " SELECT g.*  FROM sc_group_master g where " ;
            $sql .=" RAND()<(SELECT ((%d/COUNT(*))*4) FROM sc_group_master g2) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$limit,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getUserGroups($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = "select ug.* from sc_user_group ug" ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Group","ug");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= " order by ug.id LIMIT %d " ;
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPagedUserGroups($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select ug.* from sc_user_group ug" ;

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Group","ug");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction,"ug.id",$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;

        }

        static function getCountOnLoginId($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");

            $sql = "select count(id) as count from sc_user_group ug where ug.login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function setFeatureSlug($loginId,$slug) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");
            $slug = $mysqli->real_escape_string($slug);

            //operation needs admin privileges
            //read privileges from sc_user table
            $mikUserRow = \com\indigloo\sc\mysql\MikUser::getOnLoginId($loginId);
            if($mikUserRow['is_admin'] != 1 ){
                trigger_error("User does not have admin rights", E_USER_ERROR);
            }

            $sql = "update sc_feature_group set slug = '%s' where id = 1 ";
            $sql = sprintf($sql,$slug);
            MySQL\Helper::executeSQL($mysqli,$sql);

        }

        static function getFeatureSlug() {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "select slug from sc_feature_group where id = 1 " ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function process($postId,$loginId,$version,$catCode,$group_slug) {

            //sanitize input
            settype($postId,"integer");
            settype($loginId,"integer");
            settype($version,"integer");

            $sqlm1 = "insert ignore into sc_group_master(token,name,cat_code,created_on) values('%s','%s','%s',now()) ";
            $sqlm2 = "insert ignore into sc_user_group(login_id,token,name,created_on) values('%d','%s', '%s', now()) ";
            $sqlm3 = "update sc_site_tracker set group_flag = 1 where post_id = %d and version = %d " ;
            
            $dbh = NULL ;
            
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
                    $sql = sprintf($sqlm2,$loginId,$slug,$name);
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
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());
            }

        }

    }
}
?>
