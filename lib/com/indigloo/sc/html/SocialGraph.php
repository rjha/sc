<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\util\PseudoId ;
    
    class SocialGraph {

        static function getFollowerTile($loginId,$row) {
            $view = new \stdClass;
            $userId = $row["login_id"];
            $pubUserId = PseudoId::encode($userId);
            $pubUserUrl = "/pub/user/".$pubUserId ;
            
            $view->pubUserUrl = $pubUserUrl ;
            $view->name = $row["name"];
            $view->srcImage = $row["photo_url"];
            
            // This is for follow action on my follower's page.
            // for follow action :- I start following 
            // my follower. so following - is me.
            $view->followingId = $userId ;
            $view->followerId = $loginId ;

            $template = "/fragments/graph/follower/tile.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getFollowingTile($loginId,$row) {
            $view = new \stdClass;
            $userId = $row["login_id"];
            $pubUserId = PseudoId::encode($userId);
            $pubUserUrl = "/pub/user/".$pubUserId ;
            
            $view->pubUserUrl = $pubUserUrl ;
            $view->name = $row["name"];
            $view->srcImage = $row["photo_url"];
            
            // This is for unfollow action on my followings page.
            // I am the follower - me - loginId
            // I am following - what is in row["login_id"]
            
            $view->followingId = $userId ;
            $view->followerId = $loginId ;

            $template = "/fragments/graph/following/tile.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getFollowingTable($loginId,$rows) {
            $html = NULL ;
            $view = new \stdClass;
            
            if(!is_array($rows) || empty($rows)) {
                //no following
                $message = "You are not following anyone!" ;
                $html = Site::getNoResult($message);
                return $html ;
            }
            
            $records = array();
            
            foreach($rows as $row){
                $record = array();
                $userId = $row['login_id'];
                $pubUserId = PseudoId::encode($userId);
                $pubUserUrl = "/pub/user/".$pubUserId ;
                
                $record['pubUserUrl'] = $pubUserUrl ;
                $record['name'] = $row['name'];
                $record['followingId'] = $userId ;
                $record['followerId']= $loginId ;
                
                $records[] = $record;
            }
            
            $view->records = $records ;
            
            $template = "/fragments/graph/following/table.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }
        
        static function getFollowerTable($loginId,$rows) {
            $html = NULL ;
            $view = new \stdClass;
            
            if(!is_array($rows) || empty($rows)) {
                //no following
                $message = "No one is following you!" ;
                $html = Site::getNoResult($message);
                return $html ;
            }
            
            $records = array();
            
            foreach($rows as $row){
                $record = array();
                $userId = $row['login_id'];
                $pubUserId = PseudoId::encode($userId);
                $pubUserUrl = "/pub/user/".$pubUserId ;
                
                $record['pubUserUrl'] = $pubUserUrl ;
                $record['name'] = $row['name'];
                
                // This is for follow action on my follower's page.
                // for follow action :- I start following 
                // my follower. 
                $record['followingId'] = $userId ;
                $record['followerId']= $loginId ;
                
                $records[] = $record;
            }
            
            $view->records = $records ;
            
            $template = "/fragments/graph/follower/table.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }
        
    }
}

?>
