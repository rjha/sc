<?php

namespace com\indigloo\sc\auth {

    use \com\indigloo\Url as Url;
    use \com\indigloo\Util as Util;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\auth\User as WebglooUser ;
    use \com\indigloo\exception\UIException as UIException;

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

                if(empty($mikUser) || empty($mikUser["login_id"])) {
                    throw new UIException("Missing user data in 3mik session");
                }

                $loginId = $mikUser["login_id"];
                self::startSession($loginId, self::MIK);

            } else {
                throw new UIException("No 3mik user found in session");
            }

        }

        static function startOAuth2Session($loginId,$provider) {
           self::startSession($loginId, $provider);
        }

        private static function completeSessionAction($loginId,$name,$provider) {
            $_SESSION["gobble.error"] = 0 ;
            //@todo - on error return from this method
            set_error_handler("gobble_error_handler");
            set_exception_handler("gobble_exception_handler");

            $gWeb = \com\indigloo\core\Web::getInstance();
            $gSessionAction = $gWeb->find("global.session.action");

            if(empty($gSessionAction)) {
                return ;
            }

            // base64_decode action
            $action = base64_decode($gSessionAction);

            if($action === FALSE) { return ; }

            //json_decode session action
            $actionObj = json_decode($action);
            $endPoint = $actionObj->endPoint ;
            $params = $actionObj->params ;

            // see if one of the parameters has value {loginId}
            // update this parameter value to actual loginId
            $keys = get_object_vars($params);
            //@todo - change param to params
            foreach($keys as $key) {
                if($param->$key == "{loginId}") {
                    $param->$key = $loginId ;
                }
            }

            //inject loginId, name and provider into params
            $params->loginId = $loginId;
            $params->name = $name ;
            $params->provider = $provider ;

            //Facade for session action endpoint
            $facade = new \com\indigloo\sc\command\Facade();
            if($_SESSION["gobble.error"] == 0 ) {
                $response = $facade->execute($endPoint, $params);
                $message = $response["message"] ;
            }

            if($response["code"] != 200) {
                //error happened
                $logMessage = sprintf("Error completing session action [%s]",$action) ;
                Logger::getInstance()->error($logMessage);
            }

            // encode for use in url query.
            $qUrl = urlencode($actionObj->qUrl);
            if($_SESSION["gobble.error"] == 1 ) {
                $message = "gobble handler caught something fishy!" ;
            }

            // go to session action page
            $gotoUrl = "/site/go-session-action.php?q=".$qUrl."&g_message=".base64_encode($message);

            restore_error_handler();
            restore_exception_handler();

            header("Location: ".$gotoUrl);
            exit ;

        }

        private static function startSession($loginId,$provider) {

            // get denorm data on login from $userDao
            // the data in sc_user is for first time creation only
            // and denorm columns like name etc. can be stale in sc_user
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);

            $_SESSION[self::LOGIN_ID] = $loginId;
            $_SESSION[self::NAME] = $userDBRow["name"];
            $_SESSION[self::PROVIDER] = $provider;
            $_SESSION[self::TOKEN] = Util::getBase36GUID();

            // complete any pending session action.
            self::completeSessionAction($loginId,$userDBRow["name"],$provider);

        }

        static function getLoginInSession() {

            if (isset($_SESSION) && isset($_SESSION[self::TOKEN])) {
                $login = new \com\indigloo\sc\auth\view\Login();

                $login->name = $_SESSION[self::NAME] ;
                $login->provider = $_SESSION[self::PROVIDER] ;
                $login->id = $_SESSION[self::LOGIN_ID] ;
                return $login ;

            } else {
                throw new UIException("user session does not exists!");
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
                throw new UIException("No login found in session!");
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
