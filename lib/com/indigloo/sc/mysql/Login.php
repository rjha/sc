<?php
namespace com\indigloo\sc\mysql {

	use \com\indigloo\Util as Util ;
	use \com\indigloo\mysql as MySQL;

	class Login {

		const MODULE_NAME = 'com\indigloo\sc\mysql\Login';

		static function getOnId($loginId){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$loginId = $mysqli->real_escape_string($loginId);

			$sql = "select * from sc_login where id = ".$loginId ;
			$row = MySQL\Helper::fetchRow($mysqli,$sql);
			return $row ;

		}

		static function create($provider,$name){
			$mysqli = MySQL\Connection::getInstance()->getHandle();
			$sql = "insert into sc_login (provider,name,created_on) values(?,?,now()) " ;

			$code = MySQL\Connection::ACK_OK;
            $stmt = $mysqli->prepare($sql);
            $lastInsertId = NULL ;

			 if ($stmt) {
                $stmt->bind_param("ss",$provider,$name);
                $stmt->execute();
                if ($mysqli->affected_rows != 1) {
                    $code = MySQL\Error::handle(self::MODULE_NAME, $stmt);
                }
                $stmt->close();
            } else {
                $code = MySQL\Error::handle(self::MODULE_NAME, $mysqli);
            }
            
            if($code == MySQL\Connection::ACK_OK) {     
                $lastInsertId = MySQL\Connection::getInstance()->getLastInsertId();
            }
            
            return array('code' => $code , 'lastInsertId' => $lastInsertId);
		}

	}
}
?>
