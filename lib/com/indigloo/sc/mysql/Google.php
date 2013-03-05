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
        static function create(
            $googleId,
            $email,
            $name,
            $firstName,
            $lastName,
            $photo,
            $provider,
            $remoteIp){
            
             $dbh = NULL ;
             
             try {
                $sql1 = "insert into sc_login(provider,name,ip_address,created_on) " ;
                $sql1 .= " values(:provider,:name, :ip_address,now()) " ;
                
                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":provider", $provider);
                $stmt1->bindParam(":ip_address", $remoteIp);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                $sql2 = " insert into sc_google_user(google_id,email,name,first_name,last_name," ;
                $sql2 .= " photo,login_id,ip_address,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,?,?,now()) ";

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(1, $googleId);
                $stmt2->bindParam(2, $email);
                $stmt2->bindParam(3, $name);
                $stmt2->bindParam(4, $firstName);
                $stmt2->bindParam(5, $lastName);
                $stmt2->bindParam(6, $photo);
                $stmt2->bindParam(7, $loginId);
                $stmt2->bindParam(8, $remoteIp);

                $stmt2->execute();
                $stmt2 = NULL ;
                
                //Tx end
                $dbh->commit();
                $dbh = null;

                return $loginId;

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

    }
}

?>
