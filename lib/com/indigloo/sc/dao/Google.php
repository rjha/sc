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
            $row = $this->getOnId($googleId); 

            if(empty($row)){
                $message = sprintf("Login::Google:: create id %s, email %s \n",$googleId,$email);
                Logger::getInstance()->info($message);
                $loginId = mysql\Google::create($googleId,$email,$name,$firstName,$lastName,$photo) ;
                                        
            } else {
                //found
                $loginId = $row['login_id'];
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
