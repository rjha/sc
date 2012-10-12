<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\mysql as mysql;

    class Google {
        function getOrCreate($googleId,$email,$name,$firstName,$lastName,$photo) {
            $loginId = NULL ;

            //is existing record?
            $googleId = trim($googleId);
            $remoteIp =  \com\indigloo\Url::getRemoteIp();
            $row = $this->getOnId($googleId); 

            if(empty($row)){
                $message = sprintf("Login::Google::create id %s, email %s ",$googleId,$email);
                Logger::getInstance()->info($message);
                
                $provider = \com\indigloo\sc\auth\Login::GOOGLE ;
                $loginId = mysql\Google::create(
                    $googleId,
                    $email,
                    $name,
                    $firstName,
                    $lastName,
                    $photo,
                    $provider,
                    $remoteIp) ;
                                        
            } else {
                //found
                $loginId = $row['login_id'];
                mysql\Login::updateIp(session_id(),$loginId,$remoteIp);
            }

            return $loginId ;

        }

        function getOnId($googleId) {
            $row = mysql\Google::getOnId($googleId);
            return $row ;
        }
        
    }
}

?>
