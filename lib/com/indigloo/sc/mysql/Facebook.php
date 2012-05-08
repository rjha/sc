<?php

namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    use com\indigloo\sc\util\PDOWrapper;
    use \com\indigloo\exception\DBException;
    
    
    class Facebook {
        
        const MODULE_NAME = 'com\indigloo\sc\mysql\Facebook';

		static function getOnFacebookId($facebookId) {
            //@todo check if facebook_id > 64 chars
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            //facebookId is string 
			$facebookId = $mysqli->real_escape_string($facebookId);
			$sql = " select * from sc_facebook where facebook_id = '%s' " ;
            $sql = sprintf($sql,$facebookId);

			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}

		static function getOnLoginId($loginId) {
			$mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId,"integer");

			$sql = " select * from sc_facebook where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;
		}


		static function create($facebookId,$name,$firstName,$lastName,$link,$gender,$email,$provider){
            
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
                
                if(!flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB PDO Error : code is  %s",$stmt->errorCode());
                    trigger_error($message,E_USER_ERROR);
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
                
                if(!flag){
                    $dbh->rollBack();
                    $dbh = null;
                    $message = sprintf("DB Error : code is  %s",$stmt->errorCode());
                    trigger_error($message,E_USER_ERROR);
                }
                
                //Tx end
                $dbh->commit();
                $dbh = null;
                
                return $loginId;
                
            }catch (PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                Logger::getInstance()->error($e->getMessage());
                $errorCode = $e->getCode();
                $message = sprintf("Database error code %d",$errorCode);
                throw new DBException($message,$errorCode);
                
            }
           
		}

	}
}

?>
