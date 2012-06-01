<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Activity {

        static function find($subjectId,$objectId,$verb) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($subjectId,"integer");
            settype($objectId,"integer");
            settype($verb,"integer");

            $sql = " select count(id) as count from sc_activity " ;
            $sql .= " where subject_id = %d and object_id = %d and verb = %d ";
            $sql = sprintf($sql,$loginId,$itemId,$verb);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($limit,$filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select q.* , l.name as user_name from sc_post q, sc_activity a, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Activity","a");
            $q->addCondition("q.pseudo_id = a.object_id");
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
            $sql = " select count(id) as count from sc_activity";

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

            $sql = " select q.* , l.name as user_name from sc_post q, sc_activity a, sc_login l " ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\Activity","a");
            $q->addCondition("q.pseudo_id = a.object_id ");
            $q->addCondition("q.login_id = l.id");
            $q->filter($filters);
            $sql .= $q->get();

            $sql .= $q->getPagination($start,$direction,"a.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function getOnLoginId($subjectId,$verb) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($subjectId,"integer");
            settype($verb,"integer");

            $sql = " select q.* , l.name as user_name " ;
            $sql .= " from sc_post q, sc_activity a , sc_login l " ;
            $sql .= " where q.pseudo_id = a.object_id and q.login_id = l.id ";
            $sql .= " and a.subject_id = %d and a.verb = %d limit 20 ";

            $sql = sprintf($sql,$loginId,$verb);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function addPostBookmark(
                $ownerId,
                $subjectId,
                $subject,
                $objectId,
                $objectType,
                $title,
                $verb,
                $verbDesc){

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
            $sql .= " object, object_title, verb, verb_desc, created_on) " ;
            $sql .= " values(?,?,?,?,?,?,?,?,now()) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("iisissis",
                        $ownerId,
                        $subjectId,
                        $subject,
                        $objectId,
                        $objectType,
                        $title,
                        $verb,
                        $verbDesc);

                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function delete($activityId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from ac_activity where id = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$activityId) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function remove($subjectId,$objectId,$verb) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_activity where subject_id = ? and object_id = ? and verb = ? " ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("iii",$subjectId,$objectId,$verb) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }
    }
}
?>
