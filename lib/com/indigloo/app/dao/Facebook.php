<?php

namespace com\indigloo\app\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\app\mysql as mysql;

    class Facebook {

        function getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires) {

            $data = array();
            $loginId = NULL ;

            //is existing record?
            $facebookId = trim($facebookId);
            $remoteIp =  \com\indigloo\Url::getRemoteIp();
            $row = $this->getOnFacebookId($facebookId);

            if(empty($row)){
                $message = sprintf("Login::Facebook::create id %s, email %s ",$facebookId,$email);
                Logger::getInstance()->info($message);
                $source = 1 ;

                $loginId = mysql\Facebook::create(
                    $facebookId,
                    $name,
                    $firstName, 
                    $lastName,
                    $email,
                    $source,
                    $access_token,
                    $expires,
                    $remoteIp);

                $data["loginId"] = $loginId;
                $data["signup"] = true ;
                

            } else {
                // existing
                $loginId = $row["login_id"];
                $data["loginId"] = $loginId;
                $data["signup"] = false ;
                
            }

            return $data ;

        }

        function getOnFacebookId($facebookId) {
            $row = mysql\Facebook::getOnFacebookId($facebookId);
            return $row ;
        }
        
    }
}

?>
