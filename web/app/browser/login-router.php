<?php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/error.inc');

    //special error handler for this page
    set_error_handler('app_browser_errors');

    use \com\indigloo\Util;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Logger as Logger;
    
    use \com\indigloo\app\auth\Login as Login;
   	use \com\indigloo\app\mysql as mysql ;

    function raiseUIError() {
        $uimessage = "something went wrong with the signup process. Please try again." ;
        trigger_error($uimessage,E_USER_ERROR);
    }

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $fbAppSecret = Config::getInstance()->get_value("facebook.app.secret");

    $host = "http://".$_SERVER["HTTP_HOST"];
    $fbCallback = $host. "/app/browser/login-router.php";

    $code = NULL;
    $error = NULL ;

    if(array_key_exists("code",$_REQUEST)) {
        $code = $_REQUEST["code"];
    }

    if(array_key_exists("error",$_REQUEST)) {
        $error = $_REQUEST["error"] ;
        $description = $_REQUEST["error_description"] ;

        $message = sprintf(" Facebook returned error :: %s :: %s ",$error,$description);
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    if(empty($code) && empty($error)) {
        //new state token
        $stoken = Util::getMD5GUID();
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store("mik_state_token",$stoken);

        $fbDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=" .$fbAppId;
        $fbDialogUrl .= "&redirect_uri=" . urlencode($fbCallback) ."&scope=email,manage_pages,publish_stream&state=".$stoken;
        echo("<script> top.location.href='" . $fbDialogUrl . "'</script>");
        exit ;
    }

    //last state token
    $stoken = $gWeb->find("mik_state_token",true);

    if(!empty($code) && (strcmp($_REQUEST["state"],$stoken) == 0)) {

        //request to get access token
        $fbTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$fbAppId ;
        $fbTokenUrl .= "&redirect_uri=" . urlencode($fbCallback). "&client_secret=" . $fbAppSecret ;
        $fbTokenUrl .= "&code=" . $code;

        $response = file_get_contents($fbTokenUrl);
        $params = null;
        parse_str($response, $params);
        
        if(!is_array($params) && !array_key_exists("access_token",$params)) {
            $message = "Could not retrieve access_token from Facebook";
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        $expires = isset($params["expires"]) ? $params["expires"] : 3600 ;
        process_user($params["access_token"],$expires);

    }
    else {

        $message = "CSRF token returned by Facebook is different from 3mik session token";
        Logger::getInstance()->error($message);
        raiseUIError();
      
    }

    /**
     * 
     * @param access_token - access token returned by facebook for offline use
     * @param expires - time in seconds till the access_token expiry  
     * 
     * 
     */

    function process_user($access_token,$expires) {
    	
	    $graph_url = "https://graph.facebook.com/me?access_token=".$access_token;
	    $user = json_decode(file_get_contents($graph_url));

	    if(!property_exists($user,'id')) {
	        $message = "No facebook_id in graph API response" ;
	        Logger::getInstance()->error($message);
	        raiseUIError();
	    }

	    
	    $facebookId = $user->id;
	    // these properties can be missing
	    $email = property_exists($user,'email') ? $user->email : '';
	    $name = property_exists($user,'name') ? $user->name : '';
	    $firstName = property_exists($user,'first_name') ? $user->first_name : '';
	    $lastName = property_exists($user,'last_name') ? $user->last_name : '';
	    
	    $firstName = empty($firstName) ? "Anonymous" : $firstName ;
	    $name = empty($name) ? $firstName : $name ;

        $message = sprintf("canvas app login :: fb_id %d ,email %s ",$facebookId,$email);
        Logger::getInstance()->info($message);

        $facebookDao = new \com\indigloo\app\dao\Facebook();
        $data = $facebookDao->getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires);

        $loginId = $data["loginId"];
        $signup = $data["signup"];

        if(empty($loginId)) {
            $message = "Fatal error : Not able to create login" ;
            Logger::getInstance()->error($message);
            raiseUIError();
        }
        
        // success - update login record
        // start a session
        $remoteIp = \com\indigloo\Url::getRemoteIp();
        mysql\Login::updateTokenIp(session_id(),$loginId,$access_token,$expires,$remoteIp);
        $code = Login::startOAuth2Session($loginId,$name);

        $location = ($signup) ? "/app/show-page.php" : "/app/dashboard.php" ;
        header("Location: ".$location);
	        
    }

 ?>
