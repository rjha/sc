<?php
    //sc/user/account/form/mail-password.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try {

            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Email', array('maxlength' => 64, 'required' =>1));
            $fvalues = $fhandler->getValues();
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $userDao = new \com\indigloo\sc\dao\User();
            $user = $userDao->getOnEmail($fvalues['email']);

            if(empty($user)) {
                $message = "Error: We did not find any account with this email!";
                throw new UIException(array($message));
            }

            $mailDao = new \com\indigloo\sc\dao\Mail();
            $mailDao->addResetPassword($user['name'],$fvalues['email']);

            $message = "Success! You will receive an email soon!";
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_MESSAGES,array($message));
            header("Location: ".$fUrl);
            exit;

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }
    }

?>
