<?php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    //set special error handler for callback scripts
    include (APP_WEB_DIR.'/callback/error.inc');
    set_error_handler('login_error_handler');

    use com\indigloo\Util;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    use com\indigloo\Logger as Logger;
    use com\indigloo\ui\form\Message as FormMessage ;
    use \com\indigloo\sc\auth\Login as Login ;

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $fbAppSecret = Config::getInstance()->get_value("facebook.app.secret");

    $host = "http://".$_SERVER["HTTP_HOST"];
    $fbCallback = $host. "/callback/fb2.php";

    $code = NULL;
    if(array_key_exists('code',$_REQUEST)) {
        $code = $_REQUEST["code"];
    }

    $error = NULL ;
    if(array_key_exists('error',$_REQUEST)) {
        $error = $_REQUEST['error'] ;
        $description = $_REQUEST['error_description'] ;
        $message = sprintf(" Facebook returned error :: %s :: %s ",$error,$description);
        trigger_error($message,E_USER_ERROR);
    }

    if(empty($code) && empty($error)) {
        //new state token
        $stoken = Util::getMD5GUID();
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store("mik_state_token",$stoken);

        $fbDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=" .$fbAppId;
        $fbDialogUrl .= "&redirect_uri=" . urlencode($fbCallback) ."&scope=email&state=".$stoken;
        echo("<script> top.location.href='" . $fbDialogUrl . "'</script>");
        exit ;
    }

    //last state token
    $stoken = $gWeb->find('mik_state_token',true);

    if(!empty($code) && ($_REQUEST['state'] == $stoken)) {

        //request to get access token
        $fbTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$fbAppId ;
        $fbTokenUrl .= "&redirect_uri=" . urlencode($fbCallback). "&client_secret=" . $fbAppSecret ;
        $fbTokenUrl .= "&code=" . $code;

        $response = file_get_contents($fbTokenUrl);
        $params = null;
        parse_str($response, $params);

        if(!is_array($params) && !array_key_exists("access_token",$params)) {
             $message = "Could not retrieve access token from Facebook";
             trigger_error($message,E_USER_ERROR);
        }

        $graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
        $user = json_decode(file_get_contents($graph_url));

        if(!property_exists($user,'id')) {
            trigger_error("No facebook_id in graph API response", E_USER_ERROR);
        }

        processUser($user);

    }
    else {
        $message = "Facebook returned a different state token. Please try again.";
        trigger_error($message,E_USER_ERROR);
    }

    function processUser($user) {
        // exisitng record ? find on facebook_id
        // New record - create login + facebook record
        // start login session

        $id = $user->id;

        //rest of the properties may be missing
        $email = property_exists($user,'email') ? $user->email : '';
        $name = property_exists($user,'name') ? $user->name : '';
        $firstName = property_exists($user,'first_name') ? $user->first_name : '';
        $lastName = property_exists($user,'last_name') ? $user->last_name : '';
        $link = property_exists($user,'link') ? $user->link : '';
        $gender = property_exists($user,'gender') ? $user->gender : '';


        // do not what facebook will return
        // we consider auth to be good enough for a user
        if(empty($name) && empty($firstName)) {
            $name = "Anonymous" ;
        }

        $message = sprintf("Login:Facebook :: id %d ,email %s ",$id,$email);
        Logger::getInstance()->info($message);

        $facebookDao = new \com\indigloo\sc\dao\Facebook();
        $loginId = $facebookDao->getOrCreate($id,$name,$firstName,$lastName,$link,$gender,$email);

        if(empty($loginId)) {
            trigger_error("Not able to create login for facebook user",E_USER_ERROR);
        }

        Login::startOAuth2Session($loginId,Login::FACEBOOK);
        header("Location: / ");
    }

 ?>
