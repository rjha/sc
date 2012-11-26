<?php
namespace com\indigloo\sc\redis{
        
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\Constants as AppConstants;

    use \com\indigloo\sc\util\Nest as Nest;
    use \com\indigloo\connection\Redis as Redis;
    use \com\indigloo\sc\util\PseudoId ;

    /*  @see https://github.com/jdp/redisent */

    class Activity {

        function __construct() {}

        function addGlobalFeed($subjectId,$feed) {
            //@todo implementation
        }

        function addFollower($followerId,$followingId,$feed) {

            //f1->f2 (f1 is following f2)
            if ($followerId == $followingId) {
                //do not chase your own shadows!
                return;
            }

            $redis = Redis::getInstance()->connection();

            // keys
            $key1 = Nest::following("user",$followerId);
            $key2 = Nest::activities("user", $followerId);
            $key3 = Nest::followers("user",$followingId);
            $key4 = Nest::activities("user", $followingId);
            $key5 = Nest::feeds("user",$followerId);
            $key6 = Nest::feeds("user",$followingId);

            $redis->pipeline()
                    ->sadd($key1, $followingId)
                    ->lpush($key2, $feed)
                    ->sadd($key3, $followerId)
                    ->lpush($key4, $feed)
                    ->lpush($key5, $feed)
                    ->lpush($key6, $feed)
                    ->uncork();
        }

        function removeFollower($followerId, $followingId) {

            //f1->f2 (f1 is no longer following f2)
            if ($followerId == $followingId) {
                return;
            }

            $redis = Redis::getInstance()->connection();

            $key1 = Nest::following("user",$followerId);
            $key2 = Nest::followers("user",$followingId);

            // remove $followerId from $followingId's followers set
            // remove $followingId from $followerId's following set
            $redis->pipeline()
                    ->srem($key1, $followingId)
                    ->srem($key2, $followerId)
                    ->uncork();
            
        }

        function addBookmark($loginId,$itemId, $feed) {

            $redis = Redis::getInstance()->connection();

            $key1 = Nest::feeds("post",$itemId);
            $key2 = Nest::subscribers("post",$itemId);
            $key3 = Nest::activities("user",$loginId);
         
            $redis->pipeline()
                    ->lpush($key1, $feed)
                    ->sadd($key2, $loginId)
                    ->lpush($key3, $feed)
                    ->uncork();


            //fanout to subscribers of this post!
            // subscribers include the post creator 
            // and anyone who commented on or bookmarked this item
            $this->fanoutOnPost($redis, $itemId, $feed);

            //fanout to feeds of subject's (doer's) followers
            $this->fanoutOnSubject($redis, $loginId, $feed);
            
            
        }

        function addPost($loginId,$itemId,$feed) {

            $redis = Redis::getInstance()->connection();

            $key1 = Nest::activities("user",$loginId);
            $key2 = Nest::feeds("user",$loginId);
            $key3 = Nest::subscribers("post",$itemId);
            
            $redis->pipeline()
                    ->lpush($key2, $feed)
                    ->sadd($key3, $loginId)
                    ->uncork();

            // send to post creator followers
            $this->fanoutOnSubject($redis, $loginId, $feed);

        }

        function addComment($loginId,$itemId,$feed) {

            $redis = Redis::getInstance()->connection();

            $key1 = Nest::subscribers("post",$itemId);
            $key2 = Nest::activities("user",$loginId);
            $key3 = Nest::feeds("post",$itemId);

            $redis->pipeline()
                    ->sadd($key1, $loginId)
                    ->lpush($key2, $feed)
                    ->lpush($key3, $feed)
                    ->uncork();

             // send to post subscribers
            $this->fanoutOnPost($redis, $loginId, $feed);
            //send to subject's (doer) followers
            $this->fanoutOnSubject($redis, $loginId, $feed);
        
        }
        
        function fanoutOnPost($redis, $itemId, $feed) {
            //fan-out to followers
            $key = Nest::subscribers("post",$itemId) ;
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to subscriber's feeds
                $key = Nest::feeds("user",$followerId);
                $redis->lpush($key, $feed);
            }
        }
        
        function fanoutOnSubject($redis, $loginId, $feed) {
            //fan-out to followers
            $key = Nest::followers("user",$loginId);
            $followers = $redis->smembers($key);

            foreach ($followers as $followerId) {
                //push to follower's feeds
                $key = Nest::feeds("user",$followerId);
                $redis->lpush($key, $feed);
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

        function getGlobalFeeds($limit) {
            return $this->getList(Nest::global_feeds(), $limit);
        }

        function getUserActivities($loginId,$limit) {
            $key = Nest::activities("user",$loginId);
            return $this->getList($key, $limit);
        }
        
        function getUserFeeds($loginId, $limit) {
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
        
        function getPostFeeds($itemId, $limit) {
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
