<?php
namespace com\indigloo\sc\dao {

	use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

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

		function create($provider,$name){
			$data = mysql\Login::create($provider,$name);
			return $data ;
		}

        function getTotalCount($filter=NULL) {
			$dbfilter = $this->createDBFilter($filter);
			$row = mysql\Login::getTotalCount($dbfilter);
            return $row['count'] ;



        } 

	}
}
?>
