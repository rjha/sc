<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

    class SocialGraph {

        function follow($followerId,$followerName,$followingId,$followingName) {
            $row = mysql\SocialGraph::find($followerId,$followingId);
            $count = $row['count'] ;

            if($count == 0 ) {
                //actually insert
                mysql\SocialGraph::addFollower($followerId,$followingId);
                //Add to feed
                $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
                $verb = \com\indigloo\sc\Constants::FOLLOWING_VERB ;
                $feedDao->addFollower($followerId, $followerName, $followingId, $followingName, $verb);

            }

        }
        
        function unfollow($followerId,$followingId) {
            mysql\SocialGraph::removeFollower($followerId,$followingId);
            //remove from following/follower sets.
            $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
            $feedDao->removeFollower($followerId,$followingId);

        }
        
        function getFollowing($loginId,$limit) {
            $rows = mysql\SocialGraph::getFollowing($loginId,$limit);
            return $rows ;
        }
        
        function getFollowers($loginId,$limit) {
            $rows = mysql\SocialGraph::getFollowers($loginId,$limit);
            return $rows ;
        }

    }

}
?>