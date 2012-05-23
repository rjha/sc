<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\mysql as mysql;

    class Facebook {
        function getOrCreate($facebookId,$name,$firstName,$lastName,$link,$gender,$email) {
            $loginId = NULL ;

            //is existing record?
            $facebookId = trim($facebookId);
            $row = $this->getOnFacebookId($facebookId); 

            if(empty($row)){
                $message = sprintf("Login::Facebook::create id %s, email %s ",$facebookId,$email);
                Logger::getInstance()->info($message);

                //create login + facebook user
                $provider = \com\indigloo\sc\auth\Login::FACEBOOK ;
                $loginId = mysql\Facebook::create($facebookId,$name,$firstName, 
                                        $lastName,$link,$gender,$email,$provider);
            } else {
                //found
                $loginId = $row['login_id'];
            }

            return $loginId ;

        }

        function getOnFacebookId($facebookId) {
            $row = mysql\Facebook::getOnFacebookId($facebookId);
            return $row ;
        }
        
    }
}

?>
