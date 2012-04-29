<?php

    //sc/user/profile/password.php
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

    $title = $userDBRow['email'];
    $formUrl = "/user/account/form/change_password.php" ;
    include($_SERVER['APP_WEB_DIR'] . '/user/account/inc/password_form.inc');
   
?>  

