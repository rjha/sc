<?php
    //sc/user/form/login.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');


    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;

    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\auth\Login as Login ;

    if (isset($_POST['login']) && ($_POST['login'] == 'Login')) {

        $gWeb = \com\indigloo\core\Web::getInstance();
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{
            
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();
            
            //decode q param to use in redirect
            $qUrl = base64_decode($fvalues['qUrl']);

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            //canonical email - all lower case
            $email = strtolower(trim($fvalues['email']));
            $password = trim($fvalues['password']);
            $loginId = NULL ;

            try{
                $loginId = \com\indigloo\auth\User::login('sc_user',$email,$password);
            } catch(\Exception $ex) {
                $code = $ex->getCode();
                switch($code) {
                    case 401 :
                        $message = "Wrong login or password. Please try again!";
                        throw new UIException(array($message));
                    break ;
                    default:
                        $message = "Error during login. Please try after some time!";
                        throw new UIException(array($message));
                }
            } 

            //success - update login record
            // start 3mik session
            $remoteIp = \com\indigloo\Url::getRemoteIp();
            mysql\Login::updateIp(session_id(),$loginId,$remoteIp);
            $code = Login::startOAuth2Session($loginId,Login::MIK);
            
            $location = ($code == Login::FORBIDDEN_CODE) ? "/site/error/403.html"  : $qUrl ;
            header("Location: ".$location);
            exit ;

        }catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
