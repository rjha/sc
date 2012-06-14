<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\exception\DBException as DBException ;

    class Preference {

        static function get($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //sanitize input
            settype($loginId,"integer");

            $sql = " select * from sc_preference where login_id = %d " ;
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function update($loginId, $pData) {

            if(!empty($pData) && (strlen($pData) > 512)) {
                $message = " preference object is longer than db column length of 512" ;
                throw new DBException($message);
            }

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into sc_preference(login_id,p_data,created_on) " ;
            $sql .= " values(?,?,now()) ON DUPLICATE KEY UPDATE p_data = values(p_data) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("is",$loginId,$pData);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

    }
}
?>
