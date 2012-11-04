<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId ;
    
    class SocialGraph {

        private static function getTemplate($source,$typeOfUI,$hasImage) {
            $prefix = "/fragments/graph" ;
            settype($source, "integer");

            switch($source) {
                case 1 :
                    $prefix = $prefix."/follower/" ;
                    break ;
                case 2 :
                    $prefix = $prefix."/following/" ;
                    break ;
                default :
                    trigger_error("unknown social graph source", E_USER_ERROR);
            }

            $tmpl = ($hasImage) ? "image.tmpl" : "noimage.tmpl";
            $path = $prefix.$typeOfUI."/".$tmpl ;
            return $path ;

        }
        /*
         * @param $source kind of row - follower/following
         * 1 - means follower , 2 - means following
         * 
         */

        static function getWidget($loginId,$row,$source) {
            $view = self::createView($loginId,$row);
            $template = NULL;
            $hasImage = !Util::tryEmpty($view->srcImage);
            $template =  self::getTemplate($source, "widget", $hasImage) ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function createView($loginId, $row) {
            $view = new \stdClass;
            $userId = $row["login_id"];
            $pubUserId = PseudoId::encode($userId);
            $pubUserUrl = Url::base()."/pub/user/".$pubUserId ;
            
            $view->pubUserUrl = $pubUserUrl ;
            $view->name = $row["name"];
            $view->srcImage = $row["photo_url"];
            
            // This is for follow action on my follower's page.
            // for follow action :- I will start following 
            // so followerId - is me
            // for unfollow action :- I was following the user
            // so again, followerId is - me

            $view->followingId = $userId ;
            $view->followerId = $loginId ;

            return $view ;
        }


        static function getTile($loginId,$row,$source) {
            $view = self::createView($loginId,$row);
            $template = NULL;
            $hasImage = Util::tryEmpty($view->srcImage);
            $template =  self::getTemplate($source, "tile", $hasImage) ;
            $html = Template::render($template,$view);
            return $html ;

        }

        //@todo - show help card when no valid rows?
        static function getTable($loginId,$rows,$source,$withImage=false) {
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
                $record['hasImage'] = false ;

                if(!Util::tryEmpty($row["photo_url"])) {
                    $record['srcImage'] = $row["photo_url"];
                    $record['hasImage'] = true ;
                }

                $records[] = $record;
            }
            
            $view->records = $records ;
            
            settype($source,"integer");
            $template = self::getTemplate($source,"table",$withImage);
            $html = Template::render($template,$view);
            return $html ;
        }
        
    }
}

?>
