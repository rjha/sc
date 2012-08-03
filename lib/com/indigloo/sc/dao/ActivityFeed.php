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
    use \com\indigloo\sc\util\Redis as Redis;

    class ActivityFeed {

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
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:user:%s:following", $followerId);
                $key2 = sprintf("sc:user:%s:activities", $followerId);
                $key3 = sprintf("sc:user:%s:followers", $followingId);
                $key4 = sprintf("sc:user:%s:activities", $followingId);

                $key5 = sprintf("sc:user:%s:feeds", $followerId);
                $key6 = sprintf("sc:user:%s:feeds", $followingId);

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to global feeds, trim to 1000
                // Add to followers and following sets
                // Add to follower and following activities
                // Add to follower and following feeds.

                $redis->pipeline()
                        ->lpush("sc:global:queue:new", $jobId)
                        ->hset("sc:global:jobs", $jobId, $strFeedVO)
                        ->lpush("sc:global:feeds", $strFeedVO)
                        ->ltrim("sc:global:feeds", 0, 1000)
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

                $key1 = sprintf("sc:user:%s:following", $followerId);
                $key2 = sprintf("sc:user:%s:followers", $followingId);

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

                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:post:%s:feeds", $itemId);
                $key2 = sprintf("sc:post:%s:subscribers", $itemId);
                $key3 = sprintf("sc:user:%s:activities", $loginId);

                $redis->pipeline()
                        ->lpush("sc:global:queue:new", $jobId)
                        ->hset("sc:global:jobs", $jobId, $strFeedVO)
                        ->lpush("sc:global:feeds", $strFeedVO)
                        ->ltrim("sc:global:feeds", 0, 1000)
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
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:user:%s:activities", $loginId);
                $key2 = sprintf("sc:user:%s:feeds", $loginId);
                $key3 = sprintf("sc:post:%s:subscribers", $itemId);

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
                        ->lpush("sc:global:queue:new", $jobId)
                        ->hset("sc:global:jobs", $jobId, $strFeedVO)
                        ->lpush("sc:global:feeds", $strFeedVO)
                        ->ltrim("sc:global:feeds", 0, 1000)
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
                $jobId = $redis->incr("sc:global:nextJobId");
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
              
                $key1 = sprintf("sc:post:%s:subscribers", $itemId);
                $key2 = sprintf("sc:user:%s:activities", $loginId);
                $key3 = sprintf("sc:post:%s:feeds", $itemId);
                
                $redis->pipeline()
                        ->lpush("sc:global:queue:new", $jobId)
                        ->hset("sc:global:jobs", $jobId, $strFeedVO)
                        ->lpush("sc:global:feeds", $strFeedVO)
                        ->ltrim("sc:global:feeds", 0, 1000)
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
            $key = sprintf("sc:post:%s:subscribers", $itemId);
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to subscriber's feeds
                $key = sprintf("sc:user:%s:feeds", $followerId);
                $redis->lpush($key, $value);
            }
        }
        
        function fanoutOnSubject($redis, $loginId, $value) {
            //fan-out to followers
            $key = sprintf("sc:user:%s:followers", $loginId);
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to follower's feeds
                $key = sprintf("sc:user:%s:feeds", $followerId);
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
            return $this->getList("sc:global:feeds", $limit);
        }

        function getUserActivities($loginId, $limit = 50) {
            $key = sprintf("sc:user:%s:activities", $loginId);
            return $this->getList($key, $limit);
        }
        
        function getUserFeeds($loginId, $limit = 50) {
            $key = sprintf("sc:user:%s:feeds", $loginId);
            return $this->getList($key, $limit);
        }
        
        function getPostFeeds($itemId, $limit = 10) {
            $key = sprintf("sc:post:%s:feeds", $itemId);
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
