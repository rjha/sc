<?php 
	
    include 'sc-app.inc';
	include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
		
	//set special error handler for callback scripts	
    include ($_SERVER['APP_WEB_DIR'].'/callback/error.inc');
	set_error_handler('login_error_handler');
   
	use com\indigloo\Util;
	use com\indigloo\Constants as Constants;
	use com\indigloo\Configuration as Config;
	use com\indigloo\ui\form\Message as FormMessage ;
	
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
		$gWeb->store("fb_state",$stoken);
		
		$fbDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=" .$fbAppId;
		$fbDialogUrl .= "&redirect_uri=" . urlencode($fbCallback) ."&scope=email&state=".$stoken;
		echo("<script> top.location.href='" . $fbDialogUrl . "'</script>");
		exit ;
	}

	//last state token
	$stoken = $gWeb->find('fb_state',true);
	
	if(!empty($code) && ($_REQUEST['state'] == $stoken)) {
    
		//request to get access token
		$fbTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$fbAppId ;
		$fbTokenUrl .= "&redirect_uri=" . urlencode($fbCallback). "&client_secret=" . $fbAppSecret ;
		$fbTokenUrl .= "&code=" . $code;
		
		$response = file_get_contents($fbTokenUrl);
		$params = null;
		parse_str($response, $params);

		$graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
		$user = json_decode(file_get_contents($graph_url));
	 	processUser($user);


	}
	else {
		$message = "The state on 3mik.com and Faceboo do not match. You may be a victim of CSRF.";
		trigger_error($message,E_USER_ERROR);
    }

	function processUser($user) {
		// exisitng record ? find on facebook_id
		// New record - create login + facebook record
		// start login session  
		$id = $user->id;
		$name = $user->name;
		$firstName = $user->first_name ;
		$lastName = $user->last_name ;
		$link = $user->link ;
		$gender = $user->gender ;
		$email = $user->email ;

		// do not what facebook will return
		// we consider auth to be good enough for a user
		if(empty($name) && empty($firstName)) {
			$name = "Anonymous" ;
		}

		$facebookDao = new \com\indigloo\sc\dao\Facebook();
		$loginId = $facebookDao->getOrCreate($id,$name,$firstName,$lastName,$link,$gender,$email);


		if(empty($loginId)) {
			trigger_error("Not able to create login for facebook user",E_USER_ERROR);
		}

		\com\indigloo\sc\auth\Login::startFacebookSession($loginId,$name);
		header("Location: / ");
	}

 ?>
