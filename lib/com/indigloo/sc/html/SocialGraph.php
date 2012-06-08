<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\util\PseudoId ;
    
    class SocialGraph {

        static function getFollowingHtml($loginId,$rows) {
            $html = NULL ;
            $view = new \stdClass;
            
            if(!is_array($rows) || empty($rows)) {
                //no following
                $message = "You are not following anyone!" ;
                $html = NoResult::get($message);
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
            
            $template = "/fragments/graph/following-table.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }
        
        static function getFollowerHtml($loginId,$rows) {
            $html = NULL ;
            $view = new \stdClass;
            
            if(!is_array($rows) || empty($rows)) {
                //no following
                $message = "No one is following you!" ;
                $html = NoResult::get($message);
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
            
            $template = "/fragments/graph/follower-table.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }
        
    }
}

?>
