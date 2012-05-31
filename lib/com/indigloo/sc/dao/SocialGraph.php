<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;

    class SocialGraph {

        function addFollower($followerId,$followingId) {
            $row = mysql\SocialGraph::checkFollower($followerId,$followingId);
            $count = $row['count'] ;

            if($count == 0 ) {
                //actually insert
                mysql\SocialGraph::addFollower($followerId,$followingId);
            }
            
        }

    }

}
?>
