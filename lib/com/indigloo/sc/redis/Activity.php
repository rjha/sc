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
            /* do not push to following activities */
            /* $key4 = Nest::activities("user", $followingId); */
            /* do not push to follower's feed */
            /* $key5 = Nest::feeds("user",$followerId); */
            $key6 = Nest::feeds("user",$followingId);
            $key7 = Nest::score("user","followers");
            $key8 = Nest::score("user", "followings");

            $redis->pipeline()
                    ->sadd($key1, $followingId)
                    ->lpush($key2, $feed)
                    ->sadd($key3, $followerId)
                    ->lpush($key6, $feed)
                    ->zincrby($key7,1,$followingId)
                    ->zincrby($key8,1,$followerId)
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
            $key3 = Nest::score("user","followers");
            $key4 = Nest::score("user", "followings");


            // remove $followerId from $followingId's followers set
            // remove $followingId from $followerId's following set
            $redis->pipeline()
                    ->srem($key1, $followingId)
                    ->srem($key2, $followerId)
                    ->zincrby($key3,-1,$followingId)
                    ->zincrby($key4,-1,$followerId)
                    ->uncork();
            
        }

        function addBookmark($loginId,$itemId, $feed) {

            $redis = Redis::getInstance()->connection();

            //notify post subscribers + subject's followers
            $this->fanoutOnPost($redis, $itemId, $feed);
            $this->fanoutOnSubject($redis, $loginId, $feed);

            $key1 = Nest::feeds("post",$itemId);
            $key2 = Nest::subscribers("post",$itemId);
            $key3 = Nest::activities("user",$loginId);
            $key4 = Nest::score("user","likes");
            $key5 = Nest::score("post","likes");

            $redis->pipeline()
                    ->lpush($key1, $feed)
                    ->sadd($key2, $loginId)
                    ->lpush($key3, $feed)
                    ->zincrby($key4,1,$loginId)
                    ->zincrby($key5,1,$itemId)
                    ->uncork();
            
        }

        function addPost($loginId,$itemId,$feed) {

            $redis = Redis::getInstance()->connection();
            // notify subject's followers
            $this->fanoutOnSubject($redis, $loginId, $feed);

            $key1 = Nest::activities("user",$loginId);
            /* do not add post to my feed */
            /* $key2 = Nest::feeds("user",$loginId); */
            $key3 = Nest::subscribers("post",$itemId);
            $key4 = Nest::score("user","posts");


            $redis->pipeline()
                    ->lpush($key1,$feed)
                    ->sadd($key3, $loginId)
                    ->zincrby($key4,1,$itemId)
                    ->uncork();
        }

        function addComment($loginId,$itemId,$feed) {

            $redis = Redis::getInstance()->connection();

            //notify post subscribers + subject's followers
            $this->fanoutOnPost($redis, $loginId, $feed);
            $this->fanoutOnSubject($redis, $loginId, $feed);

            $key1 = Nest::subscribers("post",$itemId);
            $key2 = Nest::activities("user",$loginId);
            $key3 = Nest::feeds("post",$itemId);
            $key4 = Nest::score("post","comments");
            $key5 = Nest::score("user","comments");

            $redis->pipeline()
                    ->sadd($key1, $loginId)
                    ->lpush($key2, $feed)
                    ->lpush($key3, $feed)
                    ->zincrby($key4,1,$itemId)
                    ->zincrby($key5,1,$loginId)
                    ->uncork();
        
        }
        
        function lrem($key,$index) {
            $redis = Redis::getInstance()->connection();
            $redis->pipeline()
                ->lset($key,$index,AppConstants::REDIS_MAGIC_COOKIE)
                ->lrem($key,1,AppConstants::REDIS_MAGIC_COOKIE)
                ->uncork();

        }

        function addGlobalFeed($subjectId,$feed) {
            $redis = Redis::getInstance()->connection();
            
            $duplicate = false ;
            settype($subjectId, "integer");

            $strPop = $redis->lpop(Nest::global_feeds());
            if(!empty($strPop)) { 
                $popObj = json_decode($strPop);
                //no encoding issues
                if( ($popObj !== FALSE)
                    && ($popObj != NULL)
                    && (property_exists($popObj, "subjectId"))
                    && ($popObj->subjectId == $subjectId)) {

                    $duplicate = true ;
                }
            }

            if(!empty($strPop) && !$duplicate) {
                $redis->pipeline()
                    ->lpush(Nest::global_feeds(),$strPop)
                    ->lpush(Nest::global_feeds(), $feed)
                    ->ltrim(Nest::global_feeds(), 0, 1000)
                    ->uncork();
            }else {
                $redis->pipeline()
                    ->lpush(Nest::global_feeds(), $feed)
                    ->ltrim(Nest::global_feeds(), 0, 1000)
                    ->uncork();
            }
             
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

        function zincr($key,$entityId,$count) {
            $redis = Redis::getInstance()->connection();
            $redis->zincrby($key,$count,$entityId);
           
        }

        /* helper function to keep a redis sorted set to a certain size
         * 
         * get size of sorted set, if above limit then remove the 
         * _extra_ low-score members 
         *
         */

        function ztrim($key,$limit) {
            $redis = Redis::getInstance()->connection();
            $size = $redis->zcard($key);
            if($size > $limit) {
                $delta = ($size - $limit) ;
                $redis->zremrangebyrank($key,0,$delta-1);
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
            $feedDataObj =  $this->getList($key, $limit);
            if(sizeof($feedDataObj->feeds) == 0 ) {
                $feedDataObj = $this->getGlobalFeeds($limit);
            }

            return $feedDataObj;

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

    }
}
