<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Feedback {

		function getPaged($paginator) {
 
			$params = $paginator->getDBParams();
			$limit = $paginator->getPageSize();

			if($paginator->isHome()){
				return $this->getLatest($limit);
				
			} else {
				$start = $params['start'];
				$direction = $params['direction'];

				if(empty($start) || empty($direction)){
					trigger_error('No start or direction DB params in paginator', E_USER_ERROR);
				}

				$start = base_convert($start,36,10);
				$rows = mysql\Feedback::getPaged($start,$direction,$limit);
				return $rows ;
			}
		}
        
        function getTotalCount() {
			$row = mysql\Feedback::getTotalCount();
            return $row['count'] ;
		}


		function getLatest($limit) {
			$rows = mysql\Feedback::getLatest($limit);
			return $rows ;
		}
		
    }

}
?>
