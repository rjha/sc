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
                // get image from item.
                $postDao = new \com\indigloo\sc\dao\Post();
                $postId = PseudoId::decode($itemId);
                $image = $postDao->getImageOnId($postId);
                //actually insert
                mysql\Bookmark::add($ownerId,$loginId,$name,$itemId,"post",$title,$verb);
                //Add to activity feed
                $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
                $feedDao->addBookmark($ownerId,$loginId,$name,$itemId,$title,$image,$verb);

            }

        }

        function like($ownerId,$loginId,$name,$itemId,$title) {
            $verb = \com\indigloo\sc\Constants::LIKE_VERB ;
            $this->add($ownerId,$loginId,$name,$itemId,$title,$verb);
        }

        function save($ownerId,$loginId,$name,$itemId,$title) {
             $verb = \com\indigloo\sc\Constants::SAVE_VERB ;
             $this->add($ownerId,$loginId,$name,$itemId,$title,$verb);
        }

        function unsave($loginId,$itemId) {
            $verb = \com\indigloo\sc\Constants::SAVE_VERB ;
            mysql\Bookmark::remove($loginId,$itemId,$verb);
        }

        function delete($bookmarkId) {
            mysql\Bookmark::delete($bookmarkId);
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
