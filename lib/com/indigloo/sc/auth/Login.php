<?php

namespace com\indigloo\sc\auth {
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\auth\User as WebglooUser ;
    
    class Login {

        const NAME = "SC_USER_NAME";
        const LOGIN_ID = "SC_LOGIN_ID";
        const PROVIDER = "SC_USER_PROVIDER";
        const TOKEN = "SC_LOGIN_TOKEN" ;

        //providers
        const MIK = "3mik" ;
        const FACEBOOK = "facebook" ;
        const TWITTER = "twitter" ;
        const GOOGLE = "google" ;
         
        static function startMikSession() {
            
            if (isset($_SESSION) && isset($_SESSION[WebglooUser::USER_TOKEN])) {
                $mikUser = $_SESSION[WebglooUser::USER_DATA];

                if(empty($mikUser) || empty($mikUser['login_id'])) {
                    trigger_error("Missing user data in 3mik session", E_USER_ERROR);
                }

                // get denorm data on login from $userDao
                // the data in sc_user is for first time creation only
                // and denorm columns like name etc. can be stale in sc_user
                $loginId = $mikUser['login_id'];
                $userDao = new \com\indigloo\sc\dao\User();
                $userDBRow = $userDao->getOnLoginId($loginId); 

                $_SESSION[self::NAME] = $userDBRow['name'];
                $_SESSION[self::LOGIN_ID] = $loginId;
                $_SESSION[self::PROVIDER] = self::MIK;
                $_SESSION[self::TOKEN] = Util::getBase36GUID();

            } else {
                trigger_error("No 3mik user data found in session", E_USER_ERROR);
            }
    
        }

        
        static function startOAuth2Session($loginId,$name,$provider) {
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId); 

            //fetch name from sc_denorm_user table
            $_SESSION[self::LOGIN_ID] = $loginId;
            $_SESSION[self::NAME] = $userDBRow['name'];
            $_SESSION[self::PROVIDER] = $provider;
            $_SESSION[self::TOKEN] = Util::getBase36GUID();
        }

        static function getLoginInSession() {
            
            if (isset($_SESSION) && isset($_SESSION[self::TOKEN])) {
                $login = new \com\indigloo\sc\auth\view\Login();

                $login->name = $_SESSION[self::NAME] ;
                $login->provider = $_SESSION[self::PROVIDER] ;
                $login->id = $_SESSION[self::LOGIN_ID] ;
                return $login ;
                
            } else {
                trigger_error('logon session does not exists', E_USER_ERROR);
            }
            
        }

        static function tryLoginInSession() {
            
            if (isset($_SESSION) && isset($_SESSION[self::TOKEN])) {
                $login = new \com\indigloo\sc\auth\view\Login();
                $login->name = $_SESSION[self::NAME] ;
                $login->provider = $_SESSION[self::PROVIDER] ;
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
                trigger_error("No Login ID found in session" , E_USER_ERROR);
            }

            return $loginId ;
            
        }

        static function isOwner($loginId) {

            //false on NULL or empty
            if(Util::tryEmpty($loginId)){
                return false ;
            }
            
            $flag = false ;

            if (isset($_SESSION) 
                && isset($_SESSION[self::TOKEN]) 
                && isset($_SESSION[self::LOGIN_ID])
                && ($_SESSION[self::LOGIN_ID] == $loginId)) {

                $flag = true ;
            }
            
            return $flag ;
        }
        
        static function isAdmin(){
            $flag = false ;
            if (isset($_SESSION) && isset($_SESSION[WebglooUser::USER_TOKEN])) {
                $mikUser = $_SESSION[WebglooUser::USER_DATA];
                if(!empty($mikUser)) {
                    $flag = ($mikUser['is_admin'] == 1 ) ? true : false ;
                }
            }

            return $flag ;
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
