<?php 

	include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
   
   	/*
	$fbAppId = Config::getInstance()->get_value("facebook.app.id");
	$host = Url::base();
    $callbackUrl = $host."/app/canvas/index.php" ;
    
    $authDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $authDialogUrl .=  "&redirect_uri=" . urlencode($callbackUrl);
	$signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

	if (empty($data["user_id"])) {
	 	echo("<script> top.location.href='" . $auth_url . "'</script>");
	} else {
		 include(APP_WEB_DIR. '/app/canvas/main.inc');
	} */


?>
<h2> Bobo </h2>