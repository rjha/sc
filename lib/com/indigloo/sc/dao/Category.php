<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\mysql as mysql;
    
    class Category {

        function __construct() {

        }

        function getNumberOfCategories(){
            return sizeof($this->map);
        }

        function getIdNameMap() {
			$rows = mysql\Category::getIdNameMap();
            return $rows ;
        }

        function getCodeOnId($categoryId) {
			$row = mysql\Category::getCodeOnId($categoryId);
            return $row['code'] ;

        }


        function getLatest($code,$limit){
			$rows = mysql\Category::getLatest($code,$limit);
            return $rows ;
        }

       
        function getName($code) {
			$row = mysql\Category::getName($code);
            return $row['name'] ;
        }

		function getPaged($paginator,$code) {
 
			//translate the filter in terms of DB Column
			$params = $paginator->getDBParams();
			$limit = $paginator->getPageSize();

			if($paginator->isHome()){
				return $this->getLatest($code,$limit);
				
			} else {
				$start = $params['start'];
				$direction = $params['direction'];

				if(empty($start) || empty($direction)){
					trigger_error('No start or direction DB params in paginator', E_USER_ERROR);
				}

				$start = base_convert($start,36,10);
				$rows = mysql\Category::getPaged($start,$direction,$limit,$code);
				return $rows ;
			}
		}

		function getTotalCount($code) {
			$row = mysql\Category::getTotalCount($code);
            return $row['count'] ;
		}
    }

}
?>
