<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Bookmark {

        static function find($loginId,$itemId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");
            settype($itemId,"integer");
            settype($action,"integer");

            $sql = " select count(id) as count from sc_user_bookmark " ;
            $sql .= " where login_id = %d and item_id = %d and action = %d ";
            $sql = sprintf($sql,$loginId,$itemId,$action);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select q.* , l.name as user_name from sc_post q, sc_user_bookmark b, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","b");
            $q->addCondition("q.pseudo_id = b.item_id");
            $q->addCondition("q.login_id = l.id");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= "order by id desc LIMIT %d ";
            $sql = sprintf($sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getTotal($filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select count(id) as count from sc_user_bookmark";

            $q = new MySQL\Query($mysqli);
            $q->filter($filters);
            $sql .= $q->get();

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getPaged($start,$direction,$limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select q.* , l.name as user_name from sc_post q, sc_user_bookmark b, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Bookmark","b");
            $q->addCondition("q.pseudo_id = b.item_id");
            $q->addCondition("q.login_id = l.id");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= $q->getPagination($start,$direction,"b.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function getOnLoginId($loginId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");
            settype($action,"integer");

            $sql = " select q.* , l.name as user_name " ;
            $sql .= " from sc_post q, sc_user_bookmark b, sc_login l " ;
            $sql .= " where q.pseudo_id = b.item_id and q.login_id = l.id ";
            $sql .= " and b.login_id = %d and b.action = %d limit 20 ";

            $sql = sprintf($sql,$loginId,$action);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function add($loginId,$itemId,$action) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_user_bookmark(login_id,item_id,action,created_on) " ;
            $sql .= " values(?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("iii", $loginId, $itemId,$action);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function delete($bookmarkId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_user_bookmark where id = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$bookmarkId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function unfavorite($loginId,$itemId) {
            //remove a save(favorite)- code 2

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_user_bookmark where login_id = ? and item_id = ? and action = 2 " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$loginId,$itemId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }
    }
}
?>
