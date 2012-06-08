<?php

namespace com\indigloo\sc\dao {

    /**
     * ActivityFeed class uses redisent library as redis client
     * @see https://github.com/jdp/redisent
     *
     *
     */

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use  \com\indigloo\sc\util\Redis as Redis ;

    class ActivityFeed {

        function addFollower($followerId,$followerName,$followingId,$followingName,$verb) {

            //f1->f2 (f1 is following f2)
            if($followerId == $followingId) {
                //do not chase your own shadows!
                return ;
            }
            
            $feedVO = new \stdClass ;

            $feedVO->type = AppConstants::FOLLOW_FEED ;
            $feedVO->subjectId = $followerId ;
            $feedVO->objectId = $followingId ;

            $feedVO->subject = $followerName ;
            $feedVO->object = $followingName ;
            $feedVO->verb = $verb ;

            try {
                $redis = Redis::getInstance()->connection();

                // get global job queueId
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:user:%s:following",$followerId);
                $key2 = sprintf("sc:user:%s:activities",$followerId);
                $key3 = sprintf("sc:user:%s:followers",$followingId);
                $key4 = sprintf("sc:user:%s:activities",$followingId);

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to global activities, trim to 1000
                // Add to followers and following sets
                // Add to follower and following activities
                
                $redis->pipeline()
                        ->lpush("sc:global:queue:new",$jobId)
                        ->hset("sc:global:jobs",$jobId,$strFeedVO)
                        ->lpush("sc:global:activities",$strFeedVO)
                        ->ltrim("sc:global:activities",0,1000)
                        ->sadd($key1,$followingId)
                        ->lpush($key2,$strFeedVO)
                        ->sadd($key3,$followerId)
                        ->lpush($key4,$strFeedVO)
                        ->uncork();


            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }

        }
        
         function removeFollower($followerId,$followingId) {

            //f1->f2 (f1 is no longer following f2)
            if($followerId == $followingId) {
                return ;
            }
            
            try {
                $redis = Redis::getInstance()->connection();

                $key1 = sprintf("sc:user:%s:following",$followerId);
                $key2 = sprintf("sc:user:%s:followers",$followingId);
                
                // remove $followerId from $followingId's followers set
                // remove $followingId from $followerId's following set
                $redis->pipeline()
                        ->srem($key1,$followingId)
                        ->srem($key2,$followerId)
                        ->uncork();


            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }

        }
        
        function addBookmark($ownerId,$loginId,$name,$itemId,$title,$image,$verb) {

            $feedVO = new \stdClass ;
            $feedVO->type = AppConstants::BOOKMARK_FEED ;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->verb = $verb;
            $feedVO->srcImage = $image->source ;
            $feedVO->nameImage = $image->name ;

            try{
                $redis = Redis::getInstance()->connection();

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add bookmark to global activities, trim to 1000
                // Add to post activities.
                // Add this subject to post subscribers list 
                // owner is already a subscriber.
                // All followers of subject and all post subscribers 
                // will be notified.
                
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:post:%s:activities",$itemId);
                $key2 = sprintf("sc:post:%s:subscribers",$itemId);
                
                $redis->pipeline()
                        ->lpush("sc:global:queue:new",$jobId)
                        ->hset("sc:global:jobs",$jobId,$strFeedVO)
                        ->lpush("sc:global:activities",$strFeedVO)
                        ->ltrim("sc:global:activities",0,1000)
                        ->lpush($key1,$strFeedVO)
                        ->sadd($key2,$loginId)
                        ->uncork();

                //fanout to followers of the guy who liked it!
                $this->fanoutOnSubject($redis,$loginId, $strFeedVO);
                //fanout to people who subscribed to this post!
                // that would mean post creator 
                // and anyone who commented or bookmarked the item
                $this->fanoutOnPost($redis,$itemId, $strFeedVO);
                

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }

        }

        function addPost($loginId,$name,$itemId,$title,$image,$verb) {

            $feedVO = new \stdClass ;
            $feedVO->type =  AppConstants::POST_FEED ;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->verb = $verb ;
            $feedVO->srcImage = $image->source ;
            $feedVO->nameImage = $image->name ;

            try{
                $redis = Redis::getInstance()->connection();
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);
                
                $key1 = sprintf("sc:user:%s:activities",$loginId);
                $key2 = sprintf("sc:post:%s:subscribers",$itemId);
                
                
                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to  global activities, trim to 1000
                // Add to creator activities
                // Add creator to post subscriber list
                
                $redis->pipeline()
                    ->lpush("sc:global:queue:new",$jobId)
                    ->hset("sc:global:jobs",$jobId,$strFeedVO)
                    ->lpush("sc:global:activities",$strFeedVO)
                    ->ltrim("sc:global:activities",0,1000)
                    ->lpush($key1,$strFeedVO)
                    ->sadd($key2,$loginId)
                    ->uncork();

                // send to post creator followers
                $this->fanoutOnSubject($redis,$loginId, $strFeedVO);

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }

        }

        function addComment($ownerId,$loginId,$name,$itemId,$title,$content,$image,$verb) {

            $feedVO = new \stdClass ;
            $feedVO->type = AppConstants::COMMENT_FEED ;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post" ;
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->content = $content;
            $feedVO->verb = $verb;

            $feedVO->srcImage = $image->source ;
            $feedVO->nameImage = $image->name ;

            try {

                $redis = Redis::getInstance()->connection();

                // get global job queueId
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);

                // Add jobId to global:queue:new list
                // Add feed(job) to sc:global:jobs hash - jobId field
                // Add to  global activities, trim to 1000
                // The guy commenting on post becomes a subscriber to post
                // @imp: Do not add comment to post:itemId:activities
                // as we show comment separately from DB right now.
                
                $key1 = sprintf("sc:post:%s:subscribers",$itemId); 
               
                $redis->pipeline()
                    ->lpush("sc:global:queue:new",$jobId)
                    ->hset("sc:global:jobs",$jobId,$strFeedVO)
                    ->lpush("sc:global:activities",$strFeedVO)
                    ->ltrim("sc:global:activities",0,1000)
                    ->sadd($key1,$loginId)
                    ->uncork();

                //send to poster's followers
                $this->fanoutOnSubject($redis,$loginId, $strFeedVO);
                // send to post subscribers
                $this->fanoutOnPost($redis,$loginId, $strFeedVO);
                
            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }

        function fanoutOnSubject($redis,$loginId,$value) {
            //fan-out to followers
            $key = sprintf("sc:user:%s:followers",$loginId);
            $followers = $redis->smembers($key);

            foreach($followers as $followerId) {
                //push to follower's activities
                $key = sprintf("sc:user:%s:activities",$followerId);
                $redis->lpush($key,$value);

            }
        }
        
        function fanoutOnPost($redis,$itemId,$value) {
            //fan-out to followers
            $key = sprintf("sc:post:%s:subscribers",$itemId);
            $followers = $redis->smembers($key);

            foreach($followers as $followerId) {
                //push to follower's activities
                $key = sprintf("sc:user:%s:activities",$followerId);
                $redis->lpush($key,$value);

            }
        }
        
        function getList($key,$limit) {
            $feedDataObj = NULL ;

            try{
                $redis = Redis::getInstance()->connection();
                $feeds = $redis->lrange($key,0,$limit);
                //redis can return nil or empty array
                if(empty($feeds)) {
                    $feeds = array() ;
                }

                $feedDataObj = new \stdClass ;
                $feedDataObj->feeds = $feeds ;
                $feedDataObj->type = "list" ;


            } catch(\Exception $ex) {
                $feedDataObj = new \stdClass;
                $feedDataObj->error = "Error retrieving activity feed!" ;
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
            }

            return $feedDataObj;
        }

        function getGlobal($limit=100) {
            return $this->getList("sc:global:activities", $limit) ;
        }

        function getUser($loginId,$limit=50) {
            $key = sprintf("sc:user:%s:activities",$loginId);
            return $this->getList($key,$limit) ;
        }


        function getPost($itemId,$limit=10) {
            $key = sprintf("sc:post:%s:activities",$itemId);
            return $this->getList($key,$limit) ;
        }

        function logIt($strFeedVO) {
            //write to bad.feed log file
            $fhandle = NULL ;
            $logfile = Config::getInstance()->get_value("bad.feed.log");
            if (!file_exists($logfile)) {
                //create the file
                $fhandle = fopen($logfile, "x+");
            } else {
                $fhandle = fopen($logfile, "a+");
            }

            fwrite($fhandle,$strFeedVO);
            fwrite($fhandle,"\n\n");
            fclose($fhandle);
        }

    }

}
?>
