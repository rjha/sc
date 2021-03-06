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

        //codes
        const OK_CODE = 200 ;
        const FORBIDDEN_CODE = 403 ;
        
        static function startOAuth2Session($loginId,$provider) {

            // get denorm data on login from $userDao
            // the data in sc_user is for first time creation only
            // and denorm columns like name etc. can be stale in sc_user

            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);

            // is banned?
            $ban_bit = $userDBRow["bu_bit"] ;
            settype($ban_bit,"integer");

            if( $ban_bit == 1 ) {
                //donot start session
                return self::FORBIDDEN_CODE ;
            }

            //start session
            $_SESSION[self::LOGIN_ID] = $loginId;
            $_SESSION[self::NAME] = $userDBRow["name"];
            $_SESSION[self::PROVIDER] = $provider;
            $_SESSION[self::TOKEN] = Util::getBase36GUID();
            
            // complete any pending session action.
            self::completeSessionAction($loginId,$userDBRow["name"],$provider);
            
            return self::OK_CODE ;

        }
        
        /*
         * An action is normally performed against an endpoint.
         * we send a parameter object and an action to that endpoint.
         * 
         * so the dataObj needed to complete session action should have following properties
         * 
         * dataObj.endPoint 
         * dataObj.params (an object containing various parameters)
         * dataObj.params.x = xval ;
         * dataObj.params.y = yval ;
         * dataObj.params.action = ADD ; 
         * if dataObj.params.z is equal to "{loginId}" - This is a special value 
         * dataObj.params.z will be made equal to actual loginId value in session after 
         * authentication.
         * 
         * A command facade will find a suitable command for dataObj.endPoint
         * That command will be sent dataObj.params object after loginId substitution.
         * 
         */
        private static function completeSessionAction($loginId,$name,$provider) {

            $message = NULL ;
            $action = NULL ;

            try{
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

                $variables = get_object_vars($params);
                // associated array of name value pairs
                // undefines properties are returned as NULL
                //
                // @warning: the foreach value reference is maintained
                // after the loop. variable scope in PHP is at function level
                // so do not be too cute here and do not use $name => $value
                // inside loop as that conflicts with function argument "name"
                //
                // see if one of the parameters has "value" {loginId}
                // update this parameter value to actual loginId

                foreach($variables as $prop => $value) {
                    if($params->{$prop} == "{loginId}") {
                        $params->{$prop} = $loginId ;
                    }
                }

                //inject loginId, name and provider into params
                $params->loginId = $loginId;
                $params->name = $name ;
                $params->provider = $provider ;

                //Facade for session action endpoint
                $facade = new \com\indigloo\sc\command\Facade();
                $response = $facade->execute($endPoint, $params);
                $message = $response["message"] ;

                if($response["code"] == 200) {
                    // success
                    // set overlay message
                    $gWeb->store("global.overlay.message",$message);

                } else {
                    $message = sprintf("session action response code : %d",$response["code"]);
                    throw new \Exception($message) ;
                }


            } catch(\Exception $ex) {
                $message = sprintf("session action %s failed \n ",$action);
                Logger::getInstance()->error($ex->getMessage());
            }

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

            if (isset($_SESSION) 
                && isset($_SESSION[self::TOKEN])
                && isset($_SESSION[WebglooUser::USER_DATA])) {

                $baseUser = $_SESSION[WebglooUser::USER_DATA];
                if(!empty($baseUser)) {
                    $flag = ($baseUser['is_admin'] == 1 ) ? true : false ;
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

        static function hasMikLogin() {
            $login = self::tryLoginInSession();
            $flag = false ;
            if(!empty($login)) {
                if(strcmp($login->provider,self::MIK) == 0 ){
                    $flag = true ;
                }
            }

            return $flag ;
        }

    }
}
?>
