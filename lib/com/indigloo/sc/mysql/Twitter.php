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

        static function create($twitterId,$name,$screenName,$location,$image,$provider){

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

                $sql2 = " insert into sc_twitter(twitter_id,name,screen_name,location, " ;
                $sql2 .= " profile_image,login_id,created_on) " ;
                $sql2 .= " values(?,?,?,?,?,?,now()) ";

                $stmt = $dbh->prepare($sql2);
                $stmt->bindParam(1, $twitterId);
                $stmt->bindParam(2, $name);
                $stmt->bindParam(3, $screenName);
                $stmt->bindParam(4, $location);
                $stmt->bindParam(5, $image);
                $stmt->bindParam(6, $loginId);

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
