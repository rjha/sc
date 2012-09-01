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
        static function create($provider,$userName,$firstName,$lastName,$email,$password){

            $dbh = NULL ;
            
            try {

                //canonical form of email
                $email = strtolower(trim($email));
                $password = trim($password);

                $sql1 = "insert into sc_login (provider,name,created_on) values(:provider,:name,now()) " ;
                $flag = true ;

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":name", $userName);
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
                
                //@throws DBException
                \com\indigloo\auth\User::create('sc_user',
                                $firstName,
                                $lastName,
                                $userName,
                                $email,
                                $password,
                                $loginId);


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

        static function updateAccessToken($loginId, $access_token, $expires) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update sc_login set access_token = ? , expire_on = %s where id = ? " ;
            $expiresOn = "(now() + interval ".$expires. " second)";
            $sql = sprintf($sql,$expiresOn);
            
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("si",$access_token,$loginId);
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
