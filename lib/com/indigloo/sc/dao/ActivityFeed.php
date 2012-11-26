<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\sc\Constants as AppConstants;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\redis as redis ;


    class ActivityFeed {    
        
        private $proxy ;

        function __construct() {
            $this->proxy = new redis\Activity();
        }

        function getFollowingFeed($row) {

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::FOLLOW_FEED;
            $feedVO->subjectId = $row["subject_id"];
            $feedVO->objectId = $row["object_id"];
            $feedVO->subject = $row["subject"];
            $feedVO->object = $row["object"];
            $feedVO->verb = $row["verb"];

            $feed = json_encode($feedVO);
            if($feed === FALSE || $feed == 'null' || $feed == 'NULL') {
                //encoding went wrong
                $message = sprintf("problem with encoding activity row %d ",$row["id"]);
                trigger_error($message,E_USER_ERROR);
            }

            return $feed ;

        }

        function getBookmarkFeed($row) {

            $postDao = new \com\indigloo\sc\dao\Post();
            $itemId = $row["object_id"];
            $postId = PseudoId::decode($itemId);
            $image = $postDao->getImageOnId($postId);
            
            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::BOOKMARK_FEED;
            $feedVO->ownerId = $row["owner_id"];
            $feedVO->subject = $row["subject"];
            $feedVO->subjectId = $row["subject_id"];
            $feedVO->object = "post";
            $feedVO->objectId = $row["object_id"];
            $feedVO->title = $row["object"];
            $feedVO->verb = $row["verb"];
            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];

            $feed = json_encode($feedVO);
            if($feed === FALSE || $feed == 'null' || $feed == 'NULL') {
                //encoding went wrong
                $message = sprintf("problem with encoding activity row %d ",$row["id"]);
                trigger_error($message,E_USER_ERROR);
            }

            return $feed ;
        }

        function getPostFeed($row) {

            $postDao = new \com\indigloo\sc\dao\Post();

            $itemId = $row["object_id"];
            $postId = PseudoId::decode($itemId);
            $image = $postDao->getImageOnId($postId);

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::POST_FEED;
            $feedVO->subject = $row["subject"];
            $feedVO->subjectId = $row["subject_id"];
            $feedVO->object = "post";
            $feedVO->objectId = $row["object_id"];
            $feedVO->title = $row["object_id"];
            $feedVO->verb = $row["verb"];
            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];
     
         
            $feed = json_encode($feedVO);
            if($feed === FALSE || $feed == 'null' || $feed == 'NULL') {
                //encoding went wrong
                $message = sprintf("problem with encoding activity row %d ",$row["id"]);
                trigger_error($message,E_USER_ERROR);
            }

            return $feed ;
        }

         function getCommentFeed($row) {
            
            // @imp: activity row for comment stores 
            // post_id as object_id and not item_id
            $postId = $row["object_id"];
            $itemId = PseudoId::encode($postId);
            $postDao = new \com\indigloo\sc\dao\Post();
            $image = $postDao->getImageOnId($postId);

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::COMMENT_FEED;
            $feedVO->ownerId = $row["owner_id"];
            $feedVO->subject = $row["subject"];
            $feedVO->subjectId = $row["subject_id"];
            $feedVO->object = "post";
            $feedVO->objectId = $row["object_id"];
            $feedVO->title = $row["object"];
            $feedVO->content = $row["content"];
            $feedVO->verb = $row["verb"];

            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];

         
            $feed = json_encode($feedVO);
            if($feed === FALSE || $feed == 'null' || $feed == 'NULL') {
                //encoding went wrong
                $message = sprintf("problem with encoding activity row %d ",$row["id"]);
                trigger_error($message,E_USER_ERROR);
            }

            return $feed ;
        }

        function pushToRedis($row) {
            $verb = $row["verb"] ;

            switch($verb) {

                case AppConstants::FOLLOWING_VERB :
                    $feed = $this->getFollowingFeed($row);
                    $this->proxy->addFollower($row["subject_id"], $row["object_id"],$feed);
                    $this->proxy->addGlobalFeed($row["subject_id"],$feed);
                    break ;
                    //no fallthrough!
                case AppConstants::LIKE_VERB :
                    $feed = $this->getBookmarkFeed($row);
                    $this->proxy->addBookmark($row["subject_id"], $row["object_id"],$feed);
                    $this->proxy->addGlobalFeed($row["subject_id"],$feed);
                    break ;

                case AppConstants::POST_VERB :
                    $feed = $this->getPostFeed($row);
                    $this->proxy->addPost($row["subject_id"], $row["object_id"],$feed);
                    $this->proxy->addGlobalFeed($row["subject_id"],$feed);
                    break ;

                case AppConstants::COMMENT_VERB :
                    $feed = $this->getCommentFeed($row);
                    // @imp: activity row for comment stores 
                    // post_id as object_id and not item_id
                    $postId = $row["object_id"];
                    $itemId = PseudoId::encode($postId);
                    $this->proxy->addComment($row["subject_id"], $itemId,$feed);
                    $this->proxy->addGlobalFeed($row["subject_id"],$feed);
                    break ;
                    
                case AppConstants::UNFOLLOWING_VERB :
                    $this->proxy->removeFollower($row["subject_id"],$row["object_id"]);
                    break ;
                default :
                    $message = "Unknown activity verb : aborting! ";
                    trigger_error($message,E_USER_ERROR);
            }

        }

        function getList($key, $limit) {
            $feedDataObj =  $this->proxy->getList($key, $limit);
            return $feedDataObj;
        }

        function getGlobalFeeds($limit = 100) {
            return $this->proxy->getGlobalFeeds($limit);
        }

        function getUserActivities($loginId, $limit = 50) {
            return $this->proxy->getUserActivities($loginId,$limit);
        }
        
        function getUserFeeds($loginId, $limit = 50) {
            $feedDataObj = $this->proxy->getUserFeeds($loginId,$limit); 
            return $feedDataObj;
        }
        
        function getPostFeeds($itemId, $limit = 10) {
            return $this->proxy->getPostFeeds($itemId,$limit);
        }

    }

}
?>
