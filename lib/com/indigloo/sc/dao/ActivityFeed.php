<?php

namespace com\indigloo\sc\dao {

    /**
     * 
     * class to model activity feeds using redis data structures.
     * class uses redisent library as redis client
     * 
     * @see https://github.com/jdp/redisent
     *
     *
     */
    
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\Constants as AppConstants;

    use \com\indigloo\sc\util\Nest as Nest;
    use \com\indigloo\connection\Redis as Redis;
    use \com\indigloo\sc\util\PseudoId ;

    class ActivityFeed {    

        function create($row) {
            $verb = $row["verb"] ;

            switch($verb) {
                //no fallthrough!
                case AppConstants::LIKE_VERB :
                    $postDao = new \com\indigloo\sc\dao\Post();

                    $itemId = $row["object_id"];
                    $postId = PseudoId::decode($itemId);
                    $image = $postDao->getImageOnId($postId);
                    $this->addBookmark(
                        $row["owner_id"],
                        $row["subject_id"],
                        $row["subject"],
                        $row["object_id"],
                        $row["object"],
                        $image,
                        $verb);
                    break ;

                case AppConstants::POST_VERB :
                    $postDao = new \com\indigloo\sc\dao\Post();

                    $itemId = $row["object_id"];
                    $postId = PseudoId::decode($itemId);
                    $image = $postDao->getImageOnId($postId);

                    $this->addPost(
                        $row["subject_id"], 
                        $row["subject"], 
                        $row["object_id"], 
                        $row["object"],
                        $image,
                        $verb);
                    break ;

                case AppConstants::COMMENT_VERB :
                    // @imp: activity row for comment stores 
                    // post_id as object_id and not item_id
                    $postId = $row["object_id"];
                    $itemId = PseudoId::encode($postId);
                    $postDao = new \com\indigloo\sc\dao\Post();
                    $image = $postDao->getImageOnId($postId);
                    
                    $this->addComment(
                        $row["owner_id"],
                        $row["subject_id"],
                        $row["subject"],
                        $itemId,
                        $row["object"],
                        $row["content"],
                        $image,
                        $verb);
                    break ;

                case AppConstants::FOLLOWING_VERB :
                    $this->addFollower(
                        $row["subject_id"], 
                        $row["subject"], 
                        $row["object_id"], 
                        $row["object"],
                        $verb);
                    break ;
                case AppConstants::UNFOLLOWING_VERB :
                    $this->removeFollower($row["subject_id"],$row["object_id"]);
                    break ;
                default :
                    $message = "Unknown activity verb : aborting! "
                    trigger_error($message,E_USER_ERROR);
            }

        }

        function addFollower($followerId, $followerName, $followingId, $followingName, $verb) {

            //f1->f2 (f1 is following f2)
            if ($followerId == $followingId) {
                //do not chase your own shadows!
                return;
            }

            $feedVO = new \stdClass;

            $feedVO->type = AppConstants::FOLLOW_FEED;
            $feedVO->subjectId = $followerId;
            $feedVO->objectId = $followingId;

            $feedVO->subject = $followerName;
            $feedVO->object = $followingName;
            $feedVO->verb = $verb;

            try {
                $redis = Redis::getInstance()->connection();

                // get global job queueId
                $jobId = $redis->incr(Nest::jobId());
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);
                
                $key1 = Nest::following("user",$followerId);
                $key2 = Nest::activities("user", $followerId);
                $key3 = Nest::followers("user",$followingId);
                $key4 = Nest::activities("user", $followingId);
                $key5 = Nest::feeds("user",$followerId);
                $key6 = Nest::feeds("user",$followingId);

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to global feeds, trim to 1000
                // Add to followers and following sets
                // Add to follower and following activities
                // Add to follower and following feeds.

                $redis->pipeline()
                        ->lpush(Nest::queue(), $jobId)
                        ->hset(Nest::jobs(), $jobId, $strFeedVO)
                        ->lpush(Nest::global_feeds(), $strFeedVO)
                        ->ltrim(Nest::global_feeds(), 0, 1000)
                        ->sadd($key1, $followingId)
                        ->lpush($key2, $strFeedVO)
                        ->sadd($key3, $followerId)
                        ->lpush($key4, $strFeedVO)
                        ->lpush($key5, $strFeedVO)
                        ->lpush($key6, $strFeedVO)
                        ->uncork();
                
            } catch (\Exception $ex) {
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }

        function removeFollower($followerId, $followingId) {

            //f1->f2 (f1 is no longer following f2)
            if ($followerId == $followingId) {
                return;
            }

            try {
                $redis = Redis::getInstance()->connection();

                $key1 = Nest::following("user",$followerId);
                $key2 = Nest::followers("user",$followingId);

                // remove $followerId from $followingId's followers set
                // remove $followingId from $followerId's following set
                $redis->pipeline()
                        ->srem($key1, $followingId)
                        ->srem($key2, $followerId)
                        ->uncork();
                
            } catch (\Exception $ex) {
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
                //@todo - capture remove follower action in case of error.
                
            }
        }

        function addBookmark($ownerId, $loginId, $name, $itemId, $title, $image, $verb) {

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::BOOKMARK_FEED;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name;
            $feedVO->subjectId = $loginId;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId;
            $feedVO->title = $title;
            $feedVO->verb = $verb;
            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];

            try {
                $redis = Redis::getInstance()->connection();

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // 
                // 1) Add bookmark to global feeds, trim to 1000
                // 2) Add bookmark to post feeds.
                // 3) Add the subject (doer of this action ) to post subscribers 
                // 4) Add to subject's activity stream.
                // 
                // 5) push to all post subscriber's feeds.
                // 6) push to subject's followers's feed.
                // 

                $jobId = $redis->incr(Nest::jobId());
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                $key1 = Nest::feeds("post",$itemId);
                $key2 = Nest::subscribers("post",$itemId);
                $key3 = Nest::activities("user",$loginId);

                $redis->pipeline()
                        ->lpush(Nest::queue(), $jobId)
                        ->hset(Nest::jobs(), $jobId, $strFeedVO)
                        ->lpush(Nest::global_feeds(), $strFeedVO)
                        ->ltrim(Nest::global_feeds(), 0, 1000)
                        ->lpush($key1, $strFeedVO)
                        ->sadd($key2, $loginId)
                        ->lpush($key3, $strFeedVO)
                        ->uncork();


                //fanout to subscribers of this post!
                // subscribers include the post creator 
                // and anyone who commented on or bookmarked this item
                $this->fanoutOnPost($redis, $itemId, $strFeedVO);

                //fanout to feeds of subject's (doer's) followers
                $this->fanoutOnSubject($redis, $loginId, $strFeedVO);
                
            } catch (\Exception $ex) {
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }

        function addPost($loginId, $name, $itemId, $title, $image, $verb) {

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::POST_FEED;
            $feedVO->subject = $name;
            $feedVO->subjectId = $loginId;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId;
            $feedVO->title = $title;
            $feedVO->verb = $verb;
            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];

            try {
                $redis = Redis::getInstance()->connection();
                $jobId = $redis->incr(Nest::jobId());
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                $key1 = Nest::activities("user",$loginId);
                $key2 = Nest::feeds("user",$loginId);
                $key3 = Nest::subscribers("post",$itemId);
                
                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to  global feeds, trim to 1000
                // Add to post creator's activities
                // Add creator to post subscriber list
                // Add to post creator's feeds.
                // push to post creator's follower's feeds
                // @imp: there are no post subscribers yet!
                //
                

                $redis->pipeline()
                        ->lpush(Nest::queue(), $jobId)
                        ->hset(Nest::jobs(), $jobId, $strFeedVO)
                        ->lpush(Nest::global_feeds(), $strFeedVO)
                        ->ltrim(Nest::global_feeds(), 0, 1000)
                        ->lpush($key1, $strFeedVO)
                        ->lpush($key2, $strFeedVO)
                        ->sadd($key3, $loginId)
                        ->uncork();

                // send to post creator followers
                $this->fanoutOnSubject($redis, $loginId, $strFeedVO);
                
            } catch (\Exception $ex) {
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }

        function addComment($ownerId, $loginId, $name, $itemId, $title, $content, $image, $verb) {

            $feedVO = new \stdClass;
            $feedVO->type = AppConstants::COMMENT_FEED;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name;
            $feedVO->subjectId = $loginId;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId;
            $feedVO->title = $title;
            $feedVO->content = $content;
            $feedVO->verb = $verb;

            $feedVO->srcImage = $image["thumbnail"];
            $feedVO->nameImage = $image["name"];

            try {

                $redis = Redis::getInstance()->connection();

                // get global job queueId
                $jobId = $redis->incr(Nest::jobId());
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to  global feeds, trim to 1000
                // Add subject as a subscriber to post feeds
                // Add to subject (doer) user's activity.
                // Add to post feeds - we should have complete data.
                // 
                // whether we display it or not is up to us.
                // 
                
                $key1 = Nest::subscribers("post",$itemId);
                $key2 = Nest::activities("user",$loginId);
                $key3 = Nest::feeds("post",$itemId);

               
                $redis->pipeline()
                        ->lpush(Nest::queue(), $jobId)
                        ->hset(Nest::jobs(), $jobId, $strFeedVO)
                        ->lpush(Nest::global_feeds(), $strFeedVO)
                        ->ltrim(Nest::global_feeds(), 0, 1000)
                        ->sadd($key1, $loginId)
                        ->lpush($key2, $strFeedVO)
                        ->lpush($key3, $strFeedVO)
                        ->uncork();

                 // send to post subscribers
                $this->fanoutOnPost($redis, $loginId, $strFeedVO);
                //send to subject's (doer) followers
                $this->fanoutOnSubject($redis, $loginId, $strFeedVO);
               
            } catch (\Exception $ex) {
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }
        
        function fanoutOnPost($redis, $itemId, $value) {
            //fan-out to followers
            $key = Nest::subscribers("post",$itemId) ;
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to subscriber's feeds
                $key = Nest::feeds("user",$followerId);
                $redis->lpush($key, $value);
            }
        }
        
        function fanoutOnSubject($redis, $loginId, $value) {
            //fan-out to followers
            $key = Nest::followers("user",$loginId);
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to follower's feeds
                $key = Nest::feeds("user",$followerId);
                $redis->lpush($key, $value);
            }
        }
        
        function getList($key, $limit) {
            $feedDataObj = NULL;

            try {
                $redis = Redis::getInstance()->connection();
                $feeds = $redis->lrange($key, 0, $limit);
                //redis can return nil or empty array
                if (empty($feeds)) {
                    $feeds = array();
                }

                $feedDataObj = new \stdClass;
                $feedDataObj->feeds = $feeds;
                $feedDataObj->type = "list";
            } catch (\Exception $ex) {
                $feedDataObj = new \stdClass;
                $feedDataObj->error = "Error retrieving activity feed!";
                $message = sprintf("Redis Exception %s ", $ex->getMessage());
                Logger::getInstance()->error($message);
            }

            return $feedDataObj;
        }

        function getGlobalFeeds($limit = 100) {
            return $this->getList(Nest::global_feeds(), $limit);
        }

        function getUserActivities($loginId, $limit = 50) {
            $key = Nest::activities("user",$loginId);
            return $this->getList($key, $limit);
        }
        
        function getUserFeeds($loginId, $limit = 50) {
            //try fetching user feed
            $key = Nest::feeds("user",$loginId);
            $feedDataObj = $this->getList($key, $limit);
            // no activity in user's network
            // retun global feed
            if(sizeof($feedDataObj->feeds) == 0 ) {
                $feedDataObj = $this->getGlobalFeeds($limit);
            }

            return $feedDataObj;
        }
        
        function getPostFeeds($itemId, $limit = 10) {
            $key = Nest::feeds("post",$itemId);
            return $this->getList($key, $limit);
        }

        function logIt($strFeedVO) {
            //write to bad.feed log file
            $fhandle = NULL;
            $logfile = Config::getInstance()->get_value("bad.feed.log");
            if (!file_exists($logfile)) {
                //create the file
                $fhandle = fopen($logfile, "x+");
            } else {
                $fhandle = fopen($logfile, "a+");
            }

            fwrite($fhandle, $strFeedVO);
            fwrite($fhandle, "\n\n");
            fclose($fhandle);
        }

    }

}
?>
