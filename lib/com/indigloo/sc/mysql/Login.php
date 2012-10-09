<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException;


    class Login {

        static function getOnId($loginId){
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId, "integer");

            $sql = "select * from sc_login where id = %d " ;
            $sql = sprintf($sql,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;

        }

        static function getLatest($limit){
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            $sql = " select * from sc_login order by id desc limit %d ";
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        }

        /**
         * function to create a 3mik user. we populate following tables
         * sc_login
         * sc_user
         * sc_denorm_user (via a trigger)
         *
         */
        static function create(
            $provider,
            $userName,
            $firstName,
            $lastName,
            $email,
            $password,
            $remoteIp){

            $dbh = NULL ;
            
            try {

                //canonical form of email
                $email = strtolower(trim($email));
                $password = trim($password);

                $sql1 = "insert into sc_login (provider,name,ip_address,created_on) values(:provider,:name, :ip_address,now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":name", $userName);
                $stmt->bindParam(":provider", $provider);
                $stmt->bindParam(":ip_address", $remoteIp);

                $flag = $stmt->execute();

                if(!$flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    throw new DBException($message);
                }

                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");
                
                //@throws DBException
                \com\indigloo\auth\User::create('sc_user',
                                $firstName,
                                $lastName,
                                $userName,
                                $email,
                                $password,
                                $loginId,
                                $remoteIp);


                //Tx end
                $dbh->commit();
                $dbh = null;

            }catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());
            }

        }

        static function getTotalCount($filters) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " select count(id) as count from sc_login ";
            $q = new MySQL\Query($mysqli);
            $q->filter($filters);
            $condition = $q->get();

            $sql .= $condition;

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function updateTokenIp($loginId, $access_token, $expires,$remoteIp) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_login set access_token = ? , expire_on = %s, " ;
            $sql .= " ip_address = ?, updated_on = now() where id = ? " ;
            $expiresOn = "(now() + interval ".$expires. " second)";
            $sql = sprintf($sql,$expiresOn);
            
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ssi",$access_token,$remoteIp,$loginId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function updateIp($loginId,$remoteIp) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_login set ip_address = ?, updated_on = now() where id = ? " ; 
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("si",$remoteIp,$loginId);
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
