<?php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    //set special error handler for callback scripts
    include (APP_WEB_DIR.'/callback/error.inc');
    set_error_handler('login_error_handler');

    use \com\indigloo\Util;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\Logger as Logger;
    use \com\indigloo\ui\form\Message as FormMessage ;
    use \com\indigloo\sc\auth\Login as Login ;

    use \com\indigloo\sc\mysql as mysql ;

    function raiseUIError() {
        $uimessage = "something went wrong with the signup process. Please try again." ;
        trigger_error($uimessage,E_USER_ERROR);
    }

    $host = "http://".$_SERVER["HTTP_HOST"];
    $googleClientId = Config::getInstance()->get_value("google.client.id");
    $clientSecret = Config::getInstance()->get_value("google.client.secret");
    $googleCallback = $host. "/callback/google2.php" ;

    $error = NULL ;
    $code = NULL;

    if(array_key_exists("error",$_REQUEST)) {
        $error = $_REQUEST["error"] ;
        $message = sprintf("Google returned error :: %s ",$error);
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    if(array_key_exists("code",$_REQUEST)) {
        $code = $_REQUEST["code"];
    }

    if(empty($code) && empty($error)) {
        //new state token
        $stoken = Util::getMD5GUID();
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store("mik_state_token",$stoken);

        $googleAuthUrl  = "https://accounts.google.com/o/oauth2/auth?scope=" ;

        //space delimited scope
        $googleScope =  "https://www.googleapis.com/auth/userinfo.email" ;
        $googleScope =   $googleScope.Constants::SPACE."https://www.googleapis.com/auth/userinfo.profile" ;
        $googleAuthUrl .= urlencode($googleScope);

        $googleAuthUrl .= "&client_id=".$googleClientId ;
        $googleAuthUrl .= "&state=".$stoken ;
        $googleAuthUrl .= "&response_type=code" ;
        $googleAuthUrl .= "&redirect_uri=".urlencode($googleCallback) ;
        echo("<script> top.location.href='" . $googleAuthUrl . "'</script>");
        exit ;
    }

    //last mik state token
    $stoken = $gWeb->find("mik_state_token",true);

    if(!empty($code) && (strcmp($_REQUEST["state"],$stoken) == 0)) {

        //exchange the authorization code for an access token and refresh token
        // Google needs 5 params as HTTP POST

        $params = array('code' => $code,
                        'client_id' => $googleClientId,
                        'client_secret' => $clientSecret,
                        'redirect_uri' => $googleCallback,
                        'grant_type' => 'authorization_code');

        $fields = '';
        foreach($params as $key => $value) {
            $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');

        //get acces token
        $url = "https://accounts.google.com/o/oauth2/token" ;
        $post = curl_init($url);
        curl_setopt($post, CURLOPT_POST, true);
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_HEADER, false);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($post);
        curl_close($post);

        //parse response as JSON Object
        $jsObject = json_decode($response);

        if((json_last_error() != JSON_ERROR_NONE) || ($jsObject === NULL)) {
            //malformed json
            $message = "Google returned malformed json in response";
            Logger::getInstance()->error($message);
            raiseUIError();

        }

        //json fine but access_token is missing
        if(!property_exists($jsObject, "access_token")) {
            $message = "Could not retrieve access token from Google";
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        //Now call the userinfo  endpoint using access tokens
        $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=".$jsObject->access_token ;
        $response = file_get_contents($url) ;
        $user = json_decode($response);

        if(!property_exists($user,"id")) {
            $message = "No google_id in google userinfo endpoint response" ;
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        processUser($user);

    }
    else {

        $message = "CSRF token returned by google does not match the one stored in 3mik session";
        Logger::getInstance()->error($message);
        raiseUIError();

    }

    function processUser($user) {

        $id = $user->id;

        if(empty($id)) {
            trigger_error("Could not retrieve google id : please try again.",E_USER_ERROR);
        }

        //rest of the properties may be missing
        $email = property_exists($user,'email') ? $user->email : '';
        $name = property_exists($user,'name') ? $user->name : '';
        $firstName = property_exists($user,'given_name') ? $user->given_name : '';
        $lastName = property_exists($user,'family_name') ? $user->family_name : '';
        $photo = property_exists($user,'picture') ? $user->picture : '';

        // we consider id + auth to be good enough for a user
        if(empty($name) && empty($firstName)) {
            $name = "Anonymous" ;
        }

        $message = sprintf("Login:Google :: id %d ,email %s ",$id,$email);
        Logger::getInstance()->info($message);

        $googleDao = new \com\indigloo\sc\dao\Google();
        $loginId = $googleDao->getOrCreate($id,$email,$name,$firstName,$lastName,$photo);

        if(empty($loginId)) {
            $message = "Not able to create 3mik login for google user" ;
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        //success - update login record
        // start 3mik session
        $remoteIp = \com\indigloo\Url::getRemoteIp();
        mysql\Login::updateIp(session_id(),$loginId,$remoteIp);
        $code = Login::startOAuth2Session($loginId,Login::GOOGLE);
        
        $location = ($code == Login::FORBIDDEN_CODE) ? "/site/error/403.html"  : "/" ;
        header("Location: ".$location);
        
    }

 ?>
