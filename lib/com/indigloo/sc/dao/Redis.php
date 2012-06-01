<?php

namespace com\indigloo\sc\dao {

    /**
     * @todo wrap \redisent\RedisException  in our DBException
     * our UI does not know about redis (and does not care)
     *
     */
    class Redis {



        function addFollower($followerId,$followerName,$followingId,$followingName) {

            //f1->f2 (f1 is following f2)

            $listVO = new \stdClass ;
            $listVO->followerId = $followerId ;
            $listVO->followingId = $followingId ;

            $listVO->subject = $followerName ;
            $listVO->object = $followingName ;
            $listVO->verb = "is following" ;
            $strListVO = json_encode($listVO);

            $redis = new \redisent\Redis('redis://localhost');
            
            //Add to global activities list
            $redis->lpush('sc:global:activities',$strListVO);
            $redis->ltrim('sc:global:activities',0,1000);

            //Add to f1's following set
            $key = sprintf("sc:user:%s:following",$followerId);
            $redis->sadd($key,$followingId);

            //Add to f1's activities
            $key = sprintf("sc:user:%s:activities",$followerId);
            $redis->lpush($key,$strListVO);


            //Add to f2's followers set
            $key = sprintf("sc:user:%s:followers",$followingId);
            $redis->sadd($key,$followerId);

            //Add to f2's activities
            $key = sprintf("sc:user:%s:activities",$followingId);
            $redis->lpush($key,$strListVO);

            //no need to fan-out?
            $redis->quit();

        }

        function addBookmark($loginId,$name,$itemId,$title,$action) {
            // Add to global activities

            $listVO = new \stdClass ;
            $listVO->loginId = $loginId ;
            $listVO->itemId = $itemId ;

            $listVO->subject = $name ;
            $listVO->object = 'post';
            $listVO->title = $title ;

            $verb = NULL ;
            switch($action){
                case 'LIKE' :
                    $verb = 'liked';
                    break;
                case 'FAVORITE' :
                    $verb = 'bookmarked' ;
                    break ;
                default:
                    trigger_error('Unknown bookmark action',E_USER_ERROR);
            }

            $listVO->verb = $verb;
            $strListVO = json_encode($listVO);

            $redis = new \redisent\Redis('redis://localhost');
            //Add to global activities list
            $redis->lpush('sc:global:activities',$strListVO);
            $redis->ltrim('sc:global:activities',0,1000);

            //Add to post activities
            $postKey = sprintf("sc:post:%s:activities",$itemId);
            $redis->lpush($postKey,$strListVO);
            $this->fanOut($redis,$loginId, $strListVO);
            $redis->quit();

        }

        function addPost($loginId,$name,$itemId,$title) {
            // Add to global activities
            $listVO = new \stdClass ;

            $listVO->loginId = $loginId ;
            $listVO->itemId = $itemId ;

            $listVO->subject = $name ;
            $listVO->object = 'post';
            $listVO->title = $title ;
            $listVO->verb = 'added' ;

            $strListVO = json_encode($listVO);
            $redis = new \redisent\Redis('redis://localhost');

            //Add to global activities list
            $redis->lpush('sc:global:activities',$strListVO);
            $redis->ltrim('sc:global:activities',0,1000);
            // send to my followers
            $this->fanOut($redis,$loginId, $strListVO);
            $redis->quit();

        }

        function addComment($loginId,$name,$itemId,$title) {

            $listVO = new \stdClass ;
            $listVO->loginId = $loginId ;
            $listVO->itemId = $itemId ;

            $listVO->subject = $name ;
            $listVO->object = 'post' ;
            $listVO->title = $title ;
            $listVO->verb = 'commented';

            $strListVO = json_encode($listVO);

            $redis = new \redisent\Redis('redis://localhost');

            //Add to global activities list
            $redis->lpush('sc:global:activities',$strListVO);
            $redis->ltrim('sc:global:activities',0,1000);
            //send to my followers
            $this->fanOut($redis,$loginId, $strListVO);
            //send to owner

            $redis->quit();
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

    }

}
?>
