<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;

    class Group {
        
        static function getOnSearchIds($strIds) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $strIds = $mysqli->real_escape_string($strIds);

            $sql = " select name,token from sc_group_master g " ;
            $sql .= " where g.id in (".$strIds. ") " ;
             $sql .= " ORDER BY FIELD(g.id,".$strIds. ") " ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }
        
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

        static function getLatestUserGroups($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = "select ug.* from sc_user_group ug" ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Group","ug");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= " order by ug.id desc LIMIT %d " ;
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

            }catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());

             } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }

        }

    }
}
?>
