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

                // Add to global job queue
                // Add to global activities, trim to 1000
                // Add to followers and following sets
                // Add to follower and following activities
                $redis->pipeline()
                        ->lpush("sc:global:queue",$strFeedVO)
                        ->lpush('sc:global:activities',$strFeedVO)
                        ->ltrim('sc:global:activities',0,1000)
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

                // get global job queueId
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:post:%s:activities",$itemId);
                $key2 = sprintf("sc:user:%s:activities",$ownerId);
                $key3 = sprintf("sc:user:%s:activities",$loginId);

                $redis->pipeline()
                        ->lpush("sc:global:queue",$strFeedVO)
                        ->lpush('sc:global:activities',$strFeedVO)
                        ->ltrim('sc:global:activities',0,1000)
                        ->lpush($key1,$strFeedVO)
                        ->lpush($key2,$strFeedVO)
                        ->lpush($key3,$strFeedVO)
                        ->uncork();

                $this->fanOut($redis,$loginId, $strFeedVO);


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

                // get global job queueId
                $jobId = $redis->incr("sc:global:nextJobId");
                $feedVO->jobId = $jobId ;
                $strFeedVO = json_encode($feedVO);

                $key1 = sprintf("sc:user:%s:activities",$loginId);

                $redis->pipeline()
                    ->lpush("sc:global:queue",$strFeedVO)
                    ->lpush('sc:global:activities',$strFeedVO)
                    ->ltrim('sc:global:activities',0,1000)
                    ->lpush($key1,$strFeedVO)
                    ->uncork();

                // send to poster's followers
                $this->fanOut($redis,$loginId, $strFeedVO);

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

                $key1 = sprintf("sc:post:%s:activities",$itemId);
                $key2 = sprintf("sc:user:%s:activities",$ownerId);
                $key3 = sprintf("sc:user:%s:activities",$loginId);

                $redis->pipeline()
                    ->lpush("sc:global:queue",$strFeedVO)
                    ->lpush('sc:global:activities',$strFeedVO)
                    ->ltrim('sc:global:activities',0,1000)
                    ->lpush($key1,$strFeedVO)
                    ->lpush($key2,$strFeedVO)
                    ->lpush($key3,$strFeedVO)
                    ->uncork();

                //send to poster's followers
                $this->fanOut($redis,$loginId, $strFeedVO);

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $strFeedVO = json_encode($feedVO);
                $this->logIt($strFeedVO);
            }
        }

        function fanOut($redis,$loginId,$value) {
            //fan-out to followers
            $key = sprintf("sc:user:%s:followers",$loginId);
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

        function getGlobal() {
            return $this->getList("sc:global:activities", 100) ;
        }

        function getUser($loginId) {
            $key = sprintf("sc:user:%s:activities",$loginId);
            return $this->getList($key, 50) ;
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
