<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\util\PseudoId ;

    class Comment {

        function getOnPostId($postId) {
            $rows = mysql\Comment::getOnPostId($postId);
            return $rows ;
        }

        function getOnId($commentId) {
            $rows = mysql\Comment::getOnId($commentId);
            return $rows ;
        }

        function getPaged($paginator,$filters=array()) {

            $limit = $paginator->getPageSize();
            if($paginator->isHome()){
                return $this->getLatest($limit,$filters);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Comment::getPaged($start,$direction,$limit,$filters);
                return $rows ;
            }
        }

        function getLatest($limit,$filters=array()) {
            $rows = mysql\Comment::getLatest($limit,$filters);
            return $rows ;
        }

        function getTotalCount($filters=array()) {
            $row = mysql\Comment::getTotalCount($filters);
            return $row['count'] ;
        }

        function update($commentId,$comment) {
            $loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
            mysql\Comment::update($commentId,$comment,$loginId) ;

        }

        function create($loginId,$name,$ownerId,$postId,$title,$comment) {
            mysql\Comment::create($postId, $comment, $loginId);
            //Add to feed
            $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
            $verb = \com\indigloo\sc\Constants::COMMENT_VERB ;
            $itemId = PseudoId::encode($postId);
            $postDao = new \com\indigloo\sc\dao\Post();
            $image = $postDao->getImageOnId($postId);
            $feedDao->addComment($ownerId,$loginId,$name,$itemId,$title,$image,$verb);

        }

        function delete($commentId){
            $loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
            mysql\Comment::delete($commentId,$loginId);

        }

    }

}
?>
