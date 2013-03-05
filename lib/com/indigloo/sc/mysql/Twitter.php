<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


    class Twitter {

        static function getOnTwitterId($twitterId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            $twitterId = $mysqli->real_escape_string($twitterId);
            if(strlen($twitterId) > 64 ) {
                trigger_error("Twitter id is longer than 64 chars",E_USER_ERROR);
            }

            $sql = " select * from sc_twitter where twitter_id = '%s' " ;
            $sql = sprintf($sql,$twitterId);
            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        static function create(
            $twitterId,
            $name,
            $screenName,
            $location,
            $image,
            $provider,
            $remoteIp){

            $dbh = NULL ;
            
            try {
                $sql1 = "insert into sc_login (provider,name,ip_address,created_on) values(:provider,:name, :ip_address,now()) " ;
                
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

                $sql2 = " insert into sc_twitter(twitter_id,name,screen_name,location, " ;
                $sql2 .= " profile_image,login_id,ip_address,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,?,now()) ";

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(1, $twitterId);
                $stmt2->bindParam(2, $name);
                $stmt2->bindParam(3, $screenName);
                $stmt2->bindParam(4, $location);
                $stmt2->bindParam(5, $image);
                $stmt2->bindParam(6, $loginId);
                $stmt2->bindParam(7, $remoteIp);

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
