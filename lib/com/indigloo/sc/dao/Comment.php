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

        function create($loginId,$name,$postId,$title, $comment) {
            mysql\Comment::create($postId, $comment, $loginId);
            //push to activities
            $activityDao = new \com\indigloo\sc\dao\Activity();
            $itemId = PseudoId::encode($postId);
            $activityDao->addComment($loginId, $name, $itemId, $title);

        }

        function delete($commentId){
            $loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
            mysql\Comment::delete($commentId,$loginId);

        }

    }

}
?>
