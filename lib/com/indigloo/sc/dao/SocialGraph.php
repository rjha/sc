<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    
    class SocialGraph {

        function addFollower($followerId,$followingId) {
            $row = mysql\SocialGraph::checkFollower($followerId,$followingId);
            $count = $row['count'] ;
            $code = 0 ;
            if($count == 0 ) {
                //actually insert
                $code = mysql\SocialGraph::addFollower($followerId,$followingId);
                return $code ;
            }

            return $code ;
        }

    }

}
?>
