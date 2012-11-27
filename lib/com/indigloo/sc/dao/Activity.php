<?php

namespace com\indigloo\sc\dao {

    use \com\indigloo\sc\Constants as AppConstants;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\redis as redis ;

    use \com\indigloo\sc\Mail as WebMail ;
    use \com\indigloo\Logger;
    use \com\indigloo\sc\mysql as mysql;

    class Activity {    
        
        private $proxy ;

        function __construct() {
            $this->proxy = new redis\Activity();
        }

        function addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb,$content='') {
            mysql\Activity::addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb,$content);
        }

        function getFeed($row) {
            $verb = $row["verb"];
            $feed = NULL ;
            switch($verb) {
                case AppConstants::LIKE_VERB :
                    $feed = $this->getBookmarkFeed($row);
                    break ;
                case AppConstants::COMMENT_VERB :
                    $feed = $this->getCommentFeed($row);
                    break ;
                case AppConstants::FOLLOWING_VERB :
                    $feed = $this->getFollowingFeed($row);
                    break ;
                case AppConstants::POST_VERB :
                    $feed = $this->getPostFeed($row);
                    break ;
                default :
                    $message = "Unknown activity verb : aborting! ";
                    trigger_error($message,E_USER_ERROR);
            }
            
            return $feed ;
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
            $feed = NULL ;

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

            return $feed ;
        }

        /* @todo remove from DAO interface */
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

        function getMailflag($preferenceObj, $verb) {
            $flag = false ;
            if($verb ==  AppConstants::FOLLOWING_VERB )
                $flag = $preferenceObj->follow ;
            if($verb ==  AppConstants::COMMENT_VERB )
                $flag = $preferenceObj->comment ;
            if($verb ==  AppConstants::LIKE_VERB )
                $flag = $preferenceObj->bookmark ;

            return $flag ;
        }

        function sendMail($row,$feed) {
            
            // determine if we want to send mail for this feed
            // #1 - who is the target for this mail?
            // the guy who is the "owner", e.g when I create a post 
            // and you LIKE it, I should get a notification. 
            // so "owner of entity" is the target of our mails.
            // if X created a post and Y liked it then X gets a mail
            // if Z likes the same post then also only X gets a mail
            // Y will not receive a mail.
            
            $verb = $row["verb"];
            $ownerId = $row["owner_id"] ;
            if($verb ==  AppConstants::FOLLOWING_VERB) {
                //mail target is the guy you are following
                $ownerId = $row["object_id"];
            }

            // #2 : I am not interested in receiving mails where
            // I am the subject or doer of deed!
            if(!empty($ownerId) && ($ownerId != $row["subject_id"])) {
                // #3 - get my preference for this feed
                $preferenceDao = new \com\indigloo\sc\dao\Preference();
                $preferenceObj = $preferenceDao->get($ownerId);
                $flag = $this->getMailflag($preferenceObj,$verb);

                if($flag) {
                    $activityHtml = new \com\indigloo\sc\html\Activity();
                    $emailData = $activityHtml->getEmailData($feed);

                    if(empty($emailData)) {
                        $message = sprintf("ACTIVITY_ERROR : getting email data :id %d ",$row["id"]);
                        throw new \Exception($message);
                    }
                   
                    $text = $emailData["text"];
                    $html = $emailData["html"];
                    $userDao = new \com\indigloo\sc\dao\User();
                    $row = $userDao->getOnLoginId($ownerId);

                    $name = $row["name"];
                    $email = $row["email"];

                    if(!empty($email)) {
                         
                        $code = WebMail::sendActivityMail($name,$email,$text,$html);
                        if($code > 0 ) {
                            $message = sprintf("ACTIVITY_ERROR : sending mail : id %d ",$row["id"]);
                            throw new \Exception($message);
                        }
                    }

                } //condition:mail_flag

            }//condition:owner

        }

        function logIt($feed) {
            //write to bad.feed log file
            $fhandle = NULL;
            $logfile = Config::getInstance()->get_value("bad.feed.log");
            if (!file_exists($logfile)) {
                //create the file
                $fhandle = fopen($logfile, "x+");
            } else {
                $fhandle = fopen($logfile, "a+");
            }

            fwrite($fhandle, $feed);
            fwrite($fhandle, "\n\n");
            fclose($fhandle);
        }


    }

}
?>
