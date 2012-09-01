<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');
    require(WEBGLOO_LIB_ROOT . '/ext/twitteroauth/twitteroauth.php');

    //set special error handler for callback scripts
    include (APP_WEB_DIR.'/callback/error.inc');
    set_error_handler('login_error_handler');

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;
    use com\indigloo\Constants as Constants;
    use \com\indigloo\sc\auth\Login as Login ;

    function clearSession() {
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
    }

    function raiseUIError() {
        $uimessage = "something went wrong with the signup process. Please try again." ;
        trigger_error($uimessage,E_USER_ERROR);
    }

    /* If the oauth_token is old redirect to the login page. */
    if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
        clearSession();

        $message = "Twitter login detected an old authentication token ";
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    /* request is missing oauth verifier */
    if(!isset($_REQUEST['oauth_verifier'])) {
        clearSession();

        $message = "Twitter login :: oauth verifier is missing";
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    $appId = Config::getInstance()->get_value("twitter.app.id");
    $appSecret = Config::getInstance()->get_value("twitter.app.secret");

    $connection = new TwitterOAuth($appId, $appSecret, $_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);

    /* get access token from twitter and save in session */
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
    $_SESSION['access_token'] = $access_token;

    /* Remove no longer needed request tokens */
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);

    /* continue If HTTP response is 200 */
    if (200 == $connection->http_code) {
        processUser($connection);

    } else {
        clearSession();
        $message = "Could not retrieve Twitter access_token" ;
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    function processUser($connection) {
        $user_info = $connection->get('account/verify_credentials');
        if(isset($user_info->error)){
            $message = "Error retrieving twitter user information";
            Logger::getInstance()->error($message);
            raiseUIError();

        } else {
            // get screenName, profile Pic
            // exisitng record ? find on twitter_id
            // New record - create login + twitter record
            // start login session
            $id = $user_info->id;

            if(empty($id)) {
                trigger_error("Could not retrieve twitter id : please try again.",E_USER_ERROR);
            }

            $image = $user_info->profile_image_url;
            $screenName = $user_info->screen_name;
            $name = $user_info->name;
            $location = $user_info->location;

            // do not know what twitter will return
            // we consider auth to be good enough for a user
            if(empty($name) && empty($screenName)) {
                $name = "Anonymous" ;
            }

            $message = sprintf("Login:Twitter :: id %d ,name %s ",$id,$name);
            Logger::getInstance()->info($message);

            $twitterDao = new \com\indigloo\sc\dao\Twitter();
            $loginId = $twitterDao->getOrCreate($id,$name,$screenName,$location,$image);

            if(empty($loginId)) {
                $message = "Not able to create 3mik login for twitter user";
                Logger::getInstance()->error($message);
                raiseUIError();

            }

            Login::startOAuth2Session($loginId,Login::TWITTER);
            header("Location: / ");

        }
    }

?>
