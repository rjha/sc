<?php
    include ('sc-app.inc');
    include ($_SERVER['APP_WEB_DIR'].'/inc/header.inc');
	require($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/twitteroauth/twitteroauth.php');

	//set special error handler for callback scripts	
    include ($_SERVER['APP_WEB_DIR'].'/callback/error.inc');
	set_error_handler('login_error_handler');

	use \com\indigloo\Logger as Logger ;
	use \com\indigloo\Configuration as Config ;
	use com\indigloo\Constants as Constants;

	function clearSession() {
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
	}

	/* If the oauth_token is old redirect to the login page. */
	if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
		clearSession();
		trigger_error("Twitter login detected an old authentication token ",E_USER_ERROR);
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
		Logger::getInstance()->error("Error in Twitter get_access_token :: connection dump is :: ");
		Logger::getInstance()->dump($connection);
		clearSession();
		trigger_error("Twitter login could not find an authentication token",E_USER_ERROR);
	}
	
	function processUser($connection) {
		$user_info = $connection->get('account/verify_credentials'); 
		if(isset($user_info->error)){
			trigger_error("Error retrieving twitter user information");	
		}
		else {
			// get screenName, profile Pic 
			// exisitng record ? find on twitter_id
			// New record - create login + twitter record
			// start login session  
			$id = $user_info->id;
			$image = $user_info->profile_image_url;
			$screenName = $user_info->screen_name;
			$name = $user_info->name;
			$location = $user_info->location;
			
			// do not what twitter will return
			// we consider auth to be good enough for a user
			if(empty($name) && empty($screenName)) {
				$name = "Anonymous" ;
			}

			$twitterDao = new \com\indigloo\sc\dao\Twitter();
			$loginId = $twitterDao->getOrCreate($id,$name,$screenName,$location,$image);

			if(empty($loginId)) {
				trigger_error("Not able to create login for twitter user",E_USER_ERROR);
			}

			\com\indigloo\sc\auth\Login::startTwitterSession($loginId,$name);
			header("Location: / ");
			
		}
	}

?>
