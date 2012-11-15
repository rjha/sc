<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class User {

        static function has3MikEmail($email) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $email = $mysqli->real_escape_string($email);

            $sql = " select count(id) as count from sc_denorm_user where email = '%s' and provider = '%s' " ;
            $sql = sprintf($sql,$email, \com\indigloo\sc\auth\Login::MIK);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            
            return $row;
        }

        static function getOnId($userId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($userId,"integer");

            $sql = " select * from sc_denorm_user where id = %d " ;
            $sql = sprintf($sql,$userId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnEmail($email) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $email = $mysqli->real_escape_string($email);

            $sql = " select * from sc_denorm_user where email = '%s' " ;
            $sql = sprintf($sql,$email);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnLoginId($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId,"integer");

            $sql = " select * from sc_denorm_user where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($limit,$filters) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit,"integer");

            $sql = " select * from sc_denorm_user u" ;

            $q = new MySQL\Query($mysqli);
            $q->setAlias("com\indigloo\sc\model\User","u");
            $q->filter($filters);
            $condition = $q->get();
            $sql .= $condition;

            $sql .= " order by u.id desc LIMIT %d" ;
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

            $sql = " select u.* from sc_denorm_user u " ;

            $q = new MySQL\Query($mysqli);
            $sql .= $q->getPagination($start,$direction,"u.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function getTotal($filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select count(id) as count from sc_denorm_user ";

            $q = new MySQL\Query($mysqli);
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function update($loginId,$firstName,$lastName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl,$aboutMe) {

            $userName = $firstName. ' '.$lastName;
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_denorm_user set first_name= ?, last_name= ?, name= ?, nick_name= ?, email = ? ," ;
            $sql .= " website = ? , blog = ?, location = ?,age=?, photo_url=?, about_me = ? where login_id= ? " ;

            $stmt = $mysqli->prepare($sql);

            if($stmt) {
                $stmt->bind_param("ssssssssissi",$firstName,$lastName,$userName,$nickName,$email,
                                $website,$blog,$location,$age,$photoUrl,$aboutMe,$loginId);
                $stmt->execute();
                $stmt->close();

            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function set_bu_bit($loginId,$value,$sessionId) {
            $dbh = NULL ;
            
            try {
            
                $sql1 = "update sc_denorm_user set updated_on = now(), bu_bit = :value " ;
                $sql1 .= " where login_id = :login_id" ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->bindParam(":value", $value);
                
                $stmt1->execute();
                $stmt1 = NULL ;
                
                if(!empty($sessionId)) {
                    //clear banned user session immediately!
                    $sql2 = "delete from sc_php_session where session_id = :session_id ";
                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->bindParam(":session_id", $sessionId);
                    $stmt2->execute();
                    $stmt2 = NULL ;
                }

                //Tx end
                $dbh->commit();
                $dbh = null;
                
            } catch (\PDOException $e) {
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

        static function set_tu_bit($userId,$value) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = "update sc_denorm_user set updated_on = now(), tu_bit = ? where id = ?" ;
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ii",$value,$userId) ;
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
