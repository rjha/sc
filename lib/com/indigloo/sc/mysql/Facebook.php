<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Facebook {
        
        static function getOnFacebookId($facebookId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //facebookId is string
            //sanitize input
            $facebookId = $mysqli->real_escape_string($facebookId);
            if(strlen($facebookId) > 64 ) {
                trigger_error("Facebook id is longer than 64 chars",E_USER_ERROR);
            }

            $sql = " select * from sc_facebook where facebook_id = '%s' " ;
            $sql = sprintf($sql,$facebookId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        /**
         * function to create a facebook user data in our system. we populate the following tables
         * sc_login
         * sc_facebook
         * sc_denorm_user (via a trigger)
         * The data manipulated via our web forms is always stored in sc_denorm_table
         * sc_facebook is for first time creation only. We could have removed the columns from
         * sc_facebook that are already present in sc_denorm_user. However for lookup or other
         * purposes (e.g. sc_user.email), common columns are a necessary evil. We should never
         * update sc_facebook and other user base tables (sc_twitter, sc_user etc.) via our web forms.
         *
         *
         */
        static function create($facebookId,
            $name,
            $firstName,
            $lastName,
            $link,
            $gender,
            $email,
            $provider,
            $access_token,
            $expires){
            
             $dbh = NULL ;
             
             try {
                $sql1 = "insert into sc_login (provider,name,created_on,access_token,expire_on) " ;
                $sql1 .= " values(:provider,:name,now(),:access_token, %s) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $expiresOn = "(now() + interval ".$expires. " second)";
                $sql1 = sprintf($sql1,$expiresOn);

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":provider", $provider);
                $stmt->bindParam(":access_token", $access_token);
                
                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB PDO Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                $sql2 = " insert into sc_facebook(facebook_id,name,first_name,last_name,link,gender," ;
                $sql2 .= " email,login_id,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,?,?,now()) ";

                $stmt = $dbh->prepare($sql2);
                $stmt->bindParam(1, $facebookId);
                $stmt->bindParam(2, $name);
                $stmt->bindParam(3, $firstName);
                $stmt->bindParam(4, $lastName);
                $stmt->bindParam(5, $link);
                $stmt->bindParam(6, $gender);
                $stmt->bindParam(7, $email);
                $stmt->bindParam(8, $loginId);

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
                $dbh = NULL ;
                throw new DBException($e->getMessage(),$e->getCode());

            }

        }

    }
}

?>
