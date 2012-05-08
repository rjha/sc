<?php
namespace com\indigloo\sc\mysql {

	use \com\indigloo\Util as Util ;
	use \com\indigloo\mysql as MySQL;
    use com\indigloo\sc\util\PDOWrapper;
    use \com\indigloo\Logger ;
    use \com\indigloo\exception\DBException;
    

	class Login {

		const MODULE_NAME = 'com\indigloo\sc\mysql\Login';
		const DATE_COLUMN = "created_on" ;

		static function getOnId($loginId){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");

			$sql = "select * from sc_login where id = %d " ;
            $sql = sprintf($sql,$loginId);
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;

		}

        static function getLatest($limit){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($limit, "integer");
            $sql = " select * from sc_login order by id desc limit %d ";
            $sql = sprintf($sql,$limit);
            
			$rows = MySQL\Helper::fetchRows($mysqli,$sql);
			return $rows ;

        }

		static function create($provider,$userName,$firstName,$lastName,$email,$password){
            
            try {
                $sql1 = "insert into sc_login (provider,name,created_on) values(:provider,:name,now()) " ;
                $flag = true ;
                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                
                $stmt = $dbh->prepare($sql1);
                $stmt->bindParam(":name", $userName);
                $stmt->bindParam(":provider", $provider);
                $flag = $stmt->execute();
                
                if(!flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB PDO Error : code is  %s",$stmt->errorCode());
                    trigger_error($message,E_USER_ERROR);
                }
                
                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");
                $dbCode = \com\indigloo\auth\User::create('sc_user',
								$firstName,
                                $lastName,
								$userName,
                                $email,
								$password,
								$loginId);
                
                if($dbCode > 0 ) {
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("Database error code %d",$dbCode);
                    throw new DBException($message,$dbCode);
                }
                
                //Tx end
                $dbh->commit();
                $dbh = null;

            }catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                Logger::getInstance()->error($e->getMessage());
                $errorCode = $e->getCode();
                $message = sprintf("Database error code %d",$errorCode);
                throw new DBException($message,$errorCode);
                
            }
            
		}

        static function getTotalCount($dbfilter) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();

			$condition = '';
            if(array_key_exists(self::DATE_COLUMN,$dbfilter)) {
				$condition = " where created_on > (now() - interval 24 HOUR) ";
			}

            $sql = " select count(id) as count from sc_login ".$condition ;
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

		}

	}
}
?>
