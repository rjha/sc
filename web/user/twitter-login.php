<?php
    include ('sc-app.inc');
    include ($_SERVER['APP_WEB_DIR'].'/inc/header.inc');
	require($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/twitteroauth/twitteroauth.php');

	//set special error handler for twitter login script
    include ($_SERVER['APP_WEB_DIR'].'/callback/error.inc');
	set_error_handler('login_error_handler');

	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Logger as Logger ;

	$appId = Config::getInstance()->get_value("twitter.app.id");
	$appSecret = Config::getInstance()->get_value("twitter.app.secret");

	$connection = new TwitterOAuth($appId,$appSecret);
	
	
	$host = "http://".$_SERVER["HTTP_HOST"];
	$callBackUrl = $host .'/callback/twitter.php';
	
	//set explicit callback
	$request_token = $connection->getRequestToken($callBackUrl);
	
	// Saving them into the session
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	
	if($connection->http_code == 200){
		$url = $connection->getAuthorizeURL($request_token['oauth_token']);
		header('Location: '. $url);
		
	} else {
		Logger::getInstance()->error("Error in Twitter oauth :: connection dump ::");
		Logger::getInstance()->dump($connection);
		trigger_error("Could not connect to Twitter. Please try again later!",E_USER_ERROR);
	}

?>  
