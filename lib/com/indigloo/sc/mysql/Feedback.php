<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    class Feedback {

        static function getLatest($limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select f.* from sc_feedback f order by f.id desc limit %d " ;
            $sql = sprintf($sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getPaged($start,$direction,$limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($start,"integer");
            settype($limit,"integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select f.* from sc_feedback f " ;
            $q = new MySQL\Query($mysqli);

            $sql .= $q->getPagination($start,$direction,"f.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;

        }

        static function add($name,$email,$phone,$comment) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "insert into sc_feedback(name,email,phone,feedback,created_on) values(?,?,?,?,now()) " ;

            $stmt = $mysqli->prepare($sql);
            if($stmt) {
                $stmt->bind_param("ssss",$name,$email,$phone,$comment);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function delete($id) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "delete from sc_feedback where id = ?" ;

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i",$id) ;
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }
            
        }

    }
}
?>
