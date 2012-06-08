<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


    class Google {

        static function getOnId($googleId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $googleId = $mysqli->real_escape_string($googleId);
            if(strlen($googleId) > 64 ) {
                trigger_error("google id is longer than 64 chars",E_USER_ERROR);
            }

            $sql = " select * from sc_google_user where google_id = '%s' " ;
            $sql = sprintf($sql,$googleId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        /**
         * function to create a google user data in our system. we populate the following tables
         * sc_login
         * sc_google_user
         * sc_denorm_user (via a trigger)
         * The data manipulated via our web forms is always stored in sc_denorm_table
         * sc_google_user is for first time creation only.
         * We should never update sc_google_user via our web forms.
         *
         *
         */
        static function create($googleId,$email,$name,$firstName,$lastName,$photo,$provider){
            
             $dbh = NULL ;
             
             try {
                $sql1 = "insert into sc_login (provider,name,created_on) values(:provider,:name,now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":provider", $provider);
                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                $sql2 = " insert into sc_google_user(google_id,email,name,first_name,last_name," ;
                $sql2 .= " photo,login_id,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,?,now()) ";

                $stmt = $dbh->prepare($sql2);
                $stmt->bindParam(1, $googleId);
                $stmt->bindParam(2, $email);
                $stmt->bindParam(3, $name);
                $stmt->bindParam(4, $firstName);
                $stmt->bindParam(5, $lastName);
                $stmt->bindParam(6, $photo);
                $stmt->bindParam(7, $loginId);

                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                //Tx end
                $dbh->commit();
                $dbh = null;

                return $loginId;

            }catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                $message = sprintf("PDO error :: code %d message %s ",$e->getCode(),$e->getMessage());
                throw new DBException($message);

            }

        }

    }
}

?>
