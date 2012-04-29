<?php

    //sc/user/account/reset_password.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	
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

        //everything fine
        $title = $email;
        $formUrl = "/user/account/form/reset_password.php" ;
        include($_SERVER['APP_WEB_DIR'] . '/user/account/inc/password_form.inc');
        

    } catch(DBException $dbex) {
        $gWeb = \com\indigloo\core\Web::getInstance();
        $message = $dbex->getMessage();
        $fvalues = array('email' => $email);
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_MESSAGES,array($message));
        header("Location: /user/account/mail_password.php");
    }

?>  
