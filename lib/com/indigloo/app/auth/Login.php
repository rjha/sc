<?php

namespace com\indigloo\app\auth {

    use \com\indigloo\Url as Url;
    use \com\indigloo\Util as Util;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    
    use \com\indigloo\exception\UIException as UIException;

    class Login {

        const NAME = "SESSION_NAME";
        const LOGIN_ID = "SESSION_LOGIN_ID";
        const TOKEN = "SESSION_TOKEN" ;
       
        //codes
        const OK_CODE = 200 ;
        const FORBIDDEN_CODE = 403 ;
        
        static function startOAuth2Session($loginId,$name) {
            $_SESSION[self::LOGIN_ID] = $loginId;
            $_SESSION[self::NAME] = $name;
            $_SESSION[self::TOKEN] = Util::getBase36GUID();
            return self::OK_CODE ;

        }
        
        static function getLoginInSession() {

            if (isset($_SESSION) && isset($_SESSION[self::TOKEN])) {
                $login = new \stdClass ;
                $login->name = $_SESSION[self::NAME] ;
                $login->id = $_SESSION[self::LOGIN_ID] ;
                return $login ;

            } else {
                throw new UIException("user session does not exists!");
            }

        }

        static function tryLoginInSession() {

            if (isset($_SESSION) && isset($_SESSION[self::TOKEN])) {
                $login = new \stdClass ;
                $login->name = $_SESSION[self::NAME] ;
                $login->id = $_SESSION[self::LOGIN_ID] ;
                return $login ;

            } else {
                return NULL;
            }

        }

        static function tryLoginIdInSession() {
            $loginId = NULL ;

            if (isset($_SESSION) && isset($_SESSION[self::TOKEN]) && isset($_SESSION[self::LOGIN_ID]) ) {
                $loginId = $_SESSION[self::LOGIN_ID] ;
            }
            return $loginId ;
        }

        static function getLoginIdInSession() {
            $loginId = NULL ;
            if (isset($_SESSION) && isset($_SESSION[self::TOKEN]) && isset($_SESSION[self::LOGIN_ID]) ) {
                $loginId = $_SESSION[self::LOGIN_ID] ;
            } else{
                throw new UIException("No login found in session!");
            }

            return $loginId ;

        }

        static function hasSession(){
            $flag = false ;
            $loginId = self::tryLoginIdInSession();
            if(!is_null($loginId)) {
                $flag = true ;
            }

            return $flag ;
        }
        
    }
}
?>
