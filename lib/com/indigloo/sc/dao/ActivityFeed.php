<?php

namespace com\indigloo\sc\dao {

    /**
     * @todo - add to email queue also
     *
     */

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \redisent\RedisException as RedisException ;

    class ActivityFeed {

        function addFollower($followerId,$followerName,$followingId,$followingName,$verb) {

            //f1->f2 (f1 is following f2)

            $feedVO = new \stdClass ;

            $feedVO->type = AppConstants::FOLLOW_FEED ;
            $feedVO->followerId = $followerId ;
            $feedVO->followingId = $followingId ;

            $feedVO->followerName = $followerName ;
            $feedVO->followingName = $followingName ;
            $feedVO->verb = $verb ;
            $strFeedVO = json_encode($feedVO);

            try {
                $redis = new \redisent\Redis('redis://localhost');
                //Add to global activities list
                $redis->lpush('sc:global:activities',$strFeedVO);
                $redis->ltrim('sc:global:activities',0,1000);

                //Add to f1's following set
                $key = sprintf("sc:user:%s:following",$followerId);
                $redis->sadd($key,$followingId);

                //Add to f1's activities
                $key = sprintf("sc:user:%s:activities",$followerId);
                $redis->lpush($key,$strFeedVO);

                //Add to f2's followers set
                $key = sprintf("sc:user:%s:followers",$followingId);
                $redis->sadd($key,$followerId);

                //Add to f2's activities
                $key = sprintf("sc:user:%s:activities",$followingId);
                $redis->lpush($key,$strFeedVO);

                //no need to fan-out?
                $redis->quit();

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $this->logIt($strFeedVO);
            } 

        }

        function addBookmark($ownerId,$loginId,$name,$itemId,$title,$verb) {

            $feedVO = new \stdClass ;
            $feedVO->type = AppConstants::BOOKMARK_FEED ;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->verb = $verb;
            $strFeedVO = json_encode($feedVO);

            try{
                $redis = new \redisent\Redis('redis://localhost');
                //Add to global activities list
                $redis->lpush('sc:global:activities',$strFeedVO);
                $redis->ltrim('sc:global:activities',0,1000);

                //Add to post activities
                $postKey = sprintf("sc:post:%s:activities",$itemId);
                $redis->lpush($postKey,$strFeedVO);
                //Add to subject's followers stream
                $this->fanOut($redis,$loginId, $strFeedVO);
                //Add to owners's feed
                $key = sprintf("sc:user:%s:activities",$ownerId);
                $redis->lpush($key,$strFeedVO);
                //@todo add to email queue
                //$redis->lpush("sc:global:email",$strFeedVO);
                $redis->quit();

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $this->logIt($strFeedVO);
            } 

        }

        function addPost($loginId,$name,$itemId,$title,$verb) {
            // Add to global activities
            $feedVO = new \stdClass ;
            $feedVO->type =  AppConstants::POST_FEED ;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post";
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->verb = $verb ;
            $strFeedVO = json_encode($feedVO);

            try{
                $redis = new \redisent\Redis('redis://localhost');

                //Add to global activities list
                $redis->lpush('sc:global:activities',$strFeedVO);
                $redis->ltrim('sc:global:activities',0,1000);
                // send to my followers
                $this->fanOut($redis,$loginId, $strFeedVO);
                $redis->quit();

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
                $this->logIt($strFeedVO);
            } 

        }

        function addComment($ownerId,$loginId,$name,$itemId,$title,$verb) {

            $feedVO = new \stdClass ;
            $feedVO->type = AppConstants::COMMENT_FEED ;
            $feedVO->ownerId = $ownerId;
            $feedVO->subject = $name ;
            $feedVO->subjectId = $loginId ;
            $feedVO->object = "post" ;
            $feedVO->objectId = $itemId ;
            $feedVO->title = $title ;
            $feedVO->verb = $verb;
            $strFeedVO = json_encode($feedVO);

            try {

                $redis = new \redisent\Redis('redis://localhost');
                //Add to global activities list
                $redis->lpush('sc:global:activities',$strFeedVO);
                $redis->ltrim('sc:global:activities',0,1000);
                 //Add to post activities
                $postKey = sprintf("sc:post:%s:activities",$itemId);
                $redis->lpush($postKey,$strFeedVO);
                //send to my followers
                $this->fanOut($redis,$loginId, $strFeedVO);
                //@todo add to email queue
                $redis->quit();

            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
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

        function getGlobal() {
            $feeds = NULL ;

            try{
                $redis = new \redisent\Redis('redis://localhost');
                $feeds = $redis->lrange("sc:global:activities",0,100);
                $redis->quit();
            } catch(\Exception $ex) {
                $message = sprintf("Redis Exception %s ",$ex->getMessage());
                Logger::getInstance()->error($message);
            }

            return $feeds;

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
