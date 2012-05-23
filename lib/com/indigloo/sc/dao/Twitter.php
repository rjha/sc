<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\mysql as mysql;

    class Twitter {
        function getOrCreate($twitterId,$name,$screenName,$location,$image) {
            $loginId = NULL ;

            //is existing record?
            $twitterId = trim($twitterId);
            $row = $this->getOnTwitterId($twitterId); 

            if(empty($row)){

                $message = sprintf("Login::Twitter::create id %s ,name %s, screenname %s ",$twitterId,$name,$screenName); 
                Logger::getInstance()->info($message);

                $provider = \com\indigloo\sc\auth\Login::TWITTER ;
                $loginId = mysql\Twitter::create($twitterId,$name,$screenName,$location,$image,$provider);
                

            } else {
                //found
                $loginId = $row['login_id'];
            }

            return $loginId ;

        }

        function getOnTwitterId($twitterId) {
            $row = mysql\Twitter::getOnTwitterId($twitterId);
            return $row ;
        }
        
    }
}

?>
