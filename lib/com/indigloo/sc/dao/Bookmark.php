<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
	
    class Bookmark {

        function add($loginId,$itemId,$action) {
            $row = mysql\Bookmark::find($loginId,$itemId,$action);
            $count = $row['count'] ;
            $code = 0 ;
            if($count == 0 ) {
                //actually insert
                $code = mysql\Bookmark::add($loginId,$itemId,$action);
                return $code ;
            }

            return $code ;
        }

        function delete($bookmarkId) {
            $code = mysql\Bookmark::delete($bookmarkId);
            return $code ;
        }

        function unfavorite($loginId,$itemId) {
            $code = mysql\Bookmark::unfavorite($loginId,$itemId);
            return $code ;
        }

        function getTotal($filters=array()) {
			$row = mysql\Bookmark::getTotal($filters);
            return $row['count'];
        }

        function getLatest($limit,$filters) {
            $rows = mysql\Bookmark::getLatest($limit,$filters);
            return $rows ;
        }
        
        function getPaged($paginator,$filters) {
           	$limit = $paginator->getPageSize();
			if($paginator->isHome()){
				return $this->getLatest($limit,$filters);
			} else {

                $params = $paginator->getDBParams();
				$start = $params['start'];
				$direction = $params['direction'];
				$rows = mysql\Bookmark::getPaged($start,$direction,$limit,$filters);
				return $rows ;
			}
        }

    }

}
?>
