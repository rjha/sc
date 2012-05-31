<?php
    //sc/user/form/register.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(WEBGLOO_LIB_ROOT . '/ext/recaptchalib.php');
    require_once(WEBGLOO_LIB_ROOT. '/ext/sendgrid-php/SendGrid_loader.php');

    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;

    if (isset($_POST['register']) && ($_POST['register'] == 'Register')) {
        try{
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));

            $fvalues = $fhandler->getValues();
            $fUrl = $fvalues['fUrl'];
            $gWeb = \com\indigloo\core\Web::getInstance();

            //captcha code

            $privatekey = "6Lc3p80SAAAAABtSCxk0iHeZDRrMxvC0XTTqJpHI";
            $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

            if (!$resp->is_valid) {
                $fhandler->addError("Wrong answer to Captcha! Please try again!");
            }

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            //create a new login + user
            $loginDao = new \com\indigloo\sc\dao\Login();
            $loginDao->create($fvalues['first_name'],
                                $fvalues['last_name'],
                                $fvalues['email'],
                                $fvalues['password']);

            //success
            $gWeb->store(Constants::FORM_MESSAGES,array("Registration success! Please login."));
            header("Location: /user/login.php");

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
