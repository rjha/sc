<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

    class Bookmark {

        function add($loginId,$itemId,$action) {
            $row = mysql\Bookmark::find($loginId,$itemId,$action);
            $count = $row['count'] ;

            if($count == 0 ) {
                //actually insert
                mysql\Bookmark::add($loginId,$itemId,$action);

            }
            
        }

        function delete($bookmarkId) {
            mysql\Bookmark::delete($bookmarkId);
        }

        function unfavorite($loginId,$itemId) {
            mysql\Bookmark::unfavorite($loginId,$itemId);
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
