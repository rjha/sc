<?php

    //sc/user/account/change-password.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	 
    use com\indigloo\Util;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
	use \com\indigloo\sc\auth\Login as Login ;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
	
	$gSessionLogin = Login::getLoginInSession();
	$loginId = $gSessionLogin->id ;

    $userDao = new \com\indigloo\sc\dao\User() ;
	$userDBRow = $userDao->getonLoginId($loginId);

    //tokens for use in next screen
    $ftoken = Util::getMD5GUID();
    $email = $userDBRow['email'];
    $femail = Util::encrypt($email);
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->store("change.password.email",$femail);        
    $gWeb->store("change.password.token",$ftoken);    

    $title = $userDBRow['email'];
    $pUrl = "/user/dashboard/profile.php";
    $formUrl = "/user/account/form/change-password.php" ;
    include($_SERVER['APP_WEB_DIR'] . '/user/account/inc/password-form.inc');
   
?>  

