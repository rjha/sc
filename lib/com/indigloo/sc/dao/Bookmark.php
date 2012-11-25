<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\sc\util\PseudoId ;

    class Bookmark {

        function add($ownerId,$loginId,$name,$itemId,$title,$verb) {
            $row = mysql\Bookmark::find($loginId,$itemId,$verb);
            $count = $row['count'] ;

            if($count == 0 ) {
                mysql\Bookmark::add($ownerId,$loginId,$name,$itemId,"post",$title,$verb);
            }

        }

        function like($ownerId,$loginId,$name,$itemId,$title) {
            $verb = \com\indigloo\sc\Constants::LIKE_VERB ;
            $this->add($ownerId,$loginId,$name,$itemId,$title,$verb);
        }
        
        function delete($bookmarkId) {
            mysql\Bookmark::delete($bookmarkId);
        }
        
        // return latest posts that have been bookmarked
        function getLatest($limit,$filters) {
            $rows = mysql\Bookmark::getLatest($limit,$filters);
            return $rows ;
        }

        // return raw bookmark data from table!
        function getTableLatest($limit,$filters) {
            $rows = mysql\Bookmark::getTableLatest($limit,$filters);
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

        function getTablePaged($paginator,$filters) {
            $limit = $paginator->getPageSize();
            if($paginator->isHome()){
                return $this->getTableLatest($limit,$filters);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Bookmark::getTablePaged($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

    }

}
?>
