<?php

namespace com\indigloo\sc\facebook {

    
    use \com\indigloo\Util as CoreUtil;
    use \com\indigloo\Logger as Logger;

    class Graph {

        static function getLinkOnId($fbId) {
            if(empty($fbId)) { return NULL ; }

            $graphUrl = sprintf("http://graph.facebook.com/%s",$fbId);
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);

            //FACEBOOK GRAPH API can return true | false (surprise!)
            // php json_decode can return TRUE | FALSE | NULL for invalid input
            // use === operator to ensure TYPE check as well
            // otherwise a valid fbObject will always evaluate to TRUE with == check
            if($fbObject === FALSE || $fbObject ===  TRUE || $fbObject == NULL ) {
                $message = sprintf("Graph URL {%s} returned TRUE|FALSE|NULL",$graphUrl) ;
                Logger::getInstance()->error($message);
                return NULL ;
            }

            if(property_exists($fbObject, "error")) { 
                $message = sprintf("Graph URL %s returned error",$graphUrl) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                return NULL ; 
            }

            if(property_exists($fbObject, "id") 
                && property_exists($fbObject, "link") 
                && ($fbObject->id == $fbId)) { 
                    return $fbObject->link ;
            } else {
                $message = sprintf("Graph URL %s is missing [id|link] or has different ID",$graphUrl) ;
                Logger::getInstance()->error($message);
                return NULL ;
            }
        }

        static function getIdOnName($name) {
            if(empty($name)) { return NULL ; }

            $graphUrl = sprintf("https://graph.facebook.com/%s",$name);
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);

            if($fbObject === FALSE || $fbObject ===  TRUE || $fbObject == NULL ) {
                $message = sprintf("Graph URL %s returned TRUE|FALSE|NULL",$graphUrl) ;
                Logger::getInstance()->error($message);
                return NULL ;
            }

            if(property_exists($fbObject, "error")) { 
                $message = sprintf("Graph URL %s returned error",$graphUrl) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                return NULL ; 
            }

            if(property_exists($fbObject, "id")) { 
                return $fbObject->id ;
            } else {
                $message = sprintf("Graph URL %s returned No ID",$graphUrl) ;
                Logger::getInstance()->error($message);
                return NULL;
            }

        }


    }
}

?>
