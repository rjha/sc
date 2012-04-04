<?php

namespace com\indigloo\sc\facebook {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;

    class Graph {

        static function getLinkOnId($fbId) {
            if(empty($fbId)) { return NULL ; }

            $graphUrl = sprintf("http://graph.facebook.com/%s",$fbId);
            $response = file_get_contents($graphUrl);
          	$fbObject = json_decode($response);

            if(is_null($fbObject) || property_exists($fbObject, "error")) { return NULL ; }

            if($fbObject->id == $fbId){
                return $fbObject->link ;
            } else {
                return NULL ;
            }
        }

        static function getIdOnName($name) {
            if(empty($name)) { return NULL ; }

            $graphUrl = sprintf("https://graph.facebook.com/%s",$name);
            $response = file_get_contents($graphUrl);
          	$fbObject = json_decode($response);

            if(is_null($fbObject) || property_exists($fbObject, "error")) { return NULL ; }
            return $fbObject->id ;

        }


	}
}

?>
