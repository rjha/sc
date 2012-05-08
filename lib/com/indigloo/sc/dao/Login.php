<?php
namespace com\indigloo\sc\dao {

	use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\DBException as DBException;

	class Login {

        
		const DATE_COLUMN  = "created_on";

		function createDBFilter($filter) {
            $map = array(self::DATE_COLUMN => mysql\Login::DATE_COLUMN);
			$dbfilter = mysql\Helper::createDBFilter($filter,$map);
			return $dbfilter ;
		}

		function getOnId($loginId) {
			$row = mysql\Login::getOnId($loginId);
			return $row ;


		}

		function create($firstName,$lastName,$email,$password){
            $provider = \com\indigloo\sc\auth\Login::MIK ;
            
            if(Util::tryEmpty($firstName) || Util::tryEmpty($lastName)) {
                throw new DBException("User name is missing!",1);
            }
            
            $userName = $firstName. ' '.$lastName ;
			mysql\Login::create($provider,$userName,$firstName,$lastName,$email,$password);
			
		}

        function getTotalCount($filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$row = mysql\Login::getTotalCount($dbfilter);
            return $row['count'] ;

        } 

        function getLatest($limit) {
			$rows = mysql\Login::getLatest($limit);
            return $rows;
        }

	}
}
?>
