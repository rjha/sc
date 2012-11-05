<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Comment {

        static function getOnPostId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //sanitize input
            settype($postId,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;
            $sql .= " where l.id = a.login_id and  a.post_id = %d " ;
            $sql = sprintf($sql,$postId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnId($commentId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($commentId,"integer");

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l ";
            $sql .= " where l.id = a.login_id and a.id = %d " ;
            $sql = sprintf($sql,$commentId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //sanitize input
            settype($limit,"integer");
            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Comment","a");
            //raw condition
            $q->addCondition("l.id = a.login_id");
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;
            $sql .= " order by id desc LIMIT %d " ;
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

            $sql = " select a.*,l.name as user_name from sc_comment a,sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Comment","a");
            //raw condition
            $q->addCondition("l.id = a.login_id");
            $q->filter($filters);

            $sql .= $q->get();
            $sql .= $q->getPagination($start,$direction,"a.id",$limit);

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

        static function create($postId, $comment, $loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_comment(post_id,description,login_id, created_on) " ;
            $sql .= " values(?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("isi",$postId,$comment,$loginId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function update($commentId,$comment,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "update sc_comment set description = ?, updated_on = now() where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sii",$comment,$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function delete($commentId,$loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_comment where id = ? and login_id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$commentId,$loginId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }
            
        }
    }
}
?>
