<?php

    //sc/user/account/reset-password.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    
    use com\indigloo\Util as Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\exception\DBException as DBException;

    try{
        $token = Url::tryQueryParam('token');
        $email = Url::tryQueryParam('email');
        if(empty($token) || empty($email)) {
            printf("Required parameters are missing");
            exit ;
        }

        $email = urldecode($email);
        $mailDao = new \com\indigloo\sc\dao\Mail();
        $mailDao->checkResetPassword($email,$token);

        //tokens for use in next screen
        $ftoken = Util::getMD5GUID();
        $femail = Util::encrypt($email);
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store("change.password.email",$femail);        
        $gWeb->store("change.password.token",$ftoken);        

        $title = $email;
        $qUrl = "/user/account/login-now.php";
        $fUrl = Url::current(); 
        $submitUrl = "/user/account/form/change-password.php" ;

        include(APP_WEB_DIR . '/user/account/inc/password-form.inc');
        

    } catch(DBException $dbex) {
        $gWeb = \com\indigloo\core\Web::getInstance();
        $message = $dbex->getMessage();
        $fvalues = array('email' => $email);
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_MESSAGES,array($message));
        header("Location: /user/account/mail-password.php");
    }

?>  
