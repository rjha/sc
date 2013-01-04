<?php

namespace com\indigloo\app\mysql {

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

            $sql = " select * from app_facebook_user where facebook_id = '%s' " ;
            $sql = sprintf($sql,$facebookId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        static function create($facebookId,
                    $name,
                    $firstName, 
                    $lastName,
                    $email,
                    $source,
                    $access_token,
                    $expires,
                    $remoteIp){
            
             $dbh = NULL ;
             
             try {
                $sql1 = "insert into app_login(name,source,access_token,created_on,expire_on,ip_address) " ;
                $sql1 .= " values(:name,:source,:access_token, now(),%s, :ip_address) " ;
                
                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $expiresOn = "(now() + interval ".$expires. " second)";
                $sql1 = sprintf($sql1,$expiresOn);

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":source", $source);
                $stmt1->bindParam(":access_token", $access_token);
                $stmt1->bindParam(":ip_address", $remoteIp);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                $sql2 = " insert into app_facebook_user(facebook_id,name,first_name,last_name," ;
                $sql2 .= " email,login_id,ip_address,created_on) " ;
                $sql2 .= " values(:facebook_id, :name, :fname, :lname, :email, :login_id, :ip,now()) ";

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":facebook_id", $facebookId);
                $stmt2->bindParam(":name", $name);
                $stmt2->bindParam(":fname", $firstName);
                $stmt2->bindParam(":lname", $lastName);
                $stmt2->bindParam(":email", $email);
                $stmt2->bindParam(":login_id", $loginId);
                $stmt2->bindParam(":ip", $remoteIp);

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
