<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    class Activity {

        function addPostBookmark($ownerId,$loginId,$name,$itemId,$title,$verb,$verbDesc) {
            $row = mysql\Activity::find($loginId,$itemId,$verb);
            $count = $row['count'] ;

            if($count == 0 ) {
                //actually insert
                // find item title from itemId

                mysql\Activity::addPostBookmark($ownerId,$loginId,$name,$itemId,"post",$title,$verb,$verbDesc);
            }

        }

        function like($ownerId,$loginId,$name,$itemId,$title) {
            $verb = \com\indigloo\sc\model\Activity::LIKE_VERB ;
            $this->addPostBookmark($ownerId,$loginId,$name,$itemId,$title,$verb, "liked");
        }

        function favorite($ownerId,$loginId,$name,$itemId,$title) {
             $verb = \com\indigloo\sc\model\Activity::FAVORITE_VERB ;
             $this->addPostBookmark($ownerId,$loginId,$name,$itemId,$title,$verb, "saved");
        }

        function unfavorite($loginId,$itemId) {
            $verb = \com\indigloo\sc\model\Activity::FAVORITE_VERB ;
            mysql\Activity::remove($loginId,$itemId,$verb);
        }

        function delete($activityId) {
            mysql\Activity::delete($activityId);
        }

        function getTotal($filters=array()) {
            $row = mysql\Activity::getTotal($filters);
            return $row['count'];
        }

        function getLatest($limit,$filters) {
            $rows = mysql\Activity::getLatest($limit,$filters);
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
                $rows = mysql\Activity::getPaged($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

    }

}
?>
