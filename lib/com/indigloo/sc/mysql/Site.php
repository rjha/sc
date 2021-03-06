<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;

    class Site {

        static function getOnPostId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($postId,"integer");

            $sql = " select sm.* from sc_site_master sm, sc_post_site ps where sm.id = ps.site_id " ;
            $sql .= " and ps.post_id = %d limit 1 " ;
            $sql = sprintf($sql,$postId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getPostsOnId($siteId,$limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($siteId,"integer");
            settype($limit,"integer");

            $sql = " select p.*,l.name as user_name from sc_post_site ps, sc_post p, sc_login l " ;
            $sql .= " where l.id = p.login_id and p.id = ps.post_id and ps.site_id = %d " ;
            $sql .= " order by ps.post_id desc limit %d " ;
            $sql = sprintf($sql,$siteId,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getTotalPostsOnId($siteId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($siteId,"integer");

            $sql = " select count(ps.id)  as count from sc_post_site ps, sc_post p " ;
            $sql .= " where p.id = ps.post_id and ps.site_id = %d " ;
            $sql = sprintf($sql,$siteId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnHash($hash) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $hash = $mysqli->real_escape_string($hash);

            $sql = " select * from sc_site_master where hash = '%s' ";
            $sql = sprintf($sql,$hash);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getTmpPSData($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($postId,"integer");

            $sql = " select * from sc_tmp_ps where post_id = %d ";
            $sql = sprintf($sql,$postId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function create($hash,$host,$canonicalUrl){

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $lastInsertId = NULL;

            $sql = " insert into sc_site_master(hash,host,canonical_url,created_on) values(?,?,?,now()) " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss",$hash,$host,$canonicalUrl);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

            $lastInsertId = MySQL\Connection::getInstance()->getLastInsertId();
            return $lastInsertId;
        }

        static function deleteTmpPSData($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " delete from sc_tmp_ps where post_id = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$postId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function addTmpPSData($postId,$siteId){

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " insert into sc_tmp_ps(post_id,site_id,created_on) values(?,?,now()) " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$postId,$siteId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

            return ;
        }

        /*
         pass post_id + version to a db procedure
         inside DB procedure
         Tx
         delete rows in sc_site_post table where post_id =<in.post_id>
         do not fire delete if version = 1 (new post)
         insert new rows using SC_TMP_PS table where post_id = <in.post_id>
         update sc_site_tracker set flag = 1 where version = <in.version> and post_id = <in.post_id>
         commit
         rollback on error
         */

        static function updateTracker($postId,$version) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $stmt = $mysqli->prepare("CALL UPDATE_SITE_TRACKER(?,?)");
            $stmt->bind_param("ii",$postId,$version);
            $flag = $stmt->execute();

            if(!$flag) {
                MySQL\Error::handle($stmt);
            }

            $stmt->close();

        }

    }
}
?>
