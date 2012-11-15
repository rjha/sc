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
            $expires,
            $remoteIp){
            
             $dbh = NULL ;
             
             try {
                $sql1 = "insert into sc_login (provider,name,created_on,access_token,expire_on,ip_address) " ;
                $sql1 .= " values(:provider,:name,now(),:access_token, %s, :ip_address) " ;
                
                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $expiresOn = "(now() + interval ".$expires. " second)";
                $sql1 = sprintf($sql1,$expiresOn);

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":provider", $provider);
                $stmt1->bindParam(":access_token", $access_token);
                $stmt1->bindParam(":ip_address", $remoteIp);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                $sql2 = " insert into sc_facebook(facebook_id,name,first_name,last_name,link,gender," ;
                $sql2 .= " email,login_id,ip_address,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,?,?,?,now()) ";

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(1, $facebookId);
                $stmt2->bindParam(2, $name);
                $stmt2->bindParam(3, $firstName);
                $stmt2->bindParam(4, $lastName);
                $stmt2->bindParam(5, $link);
                $stmt2->bindParam(6, $gender);
                $stmt2->bindParam(7, $email);
                $stmt2->bindParam(8, $loginId);
                $stmt2->bindParam(9, $remoteIp);

                $stmt2->execute();
                $stmt2 = NULL ;
                

                //Tx end
                $dbh->commit();
                $dbh = null;
                return $loginId;

            }catch (\PDOException $e) {
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
