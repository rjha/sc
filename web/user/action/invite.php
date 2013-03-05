<?php
    //user/action/invite.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Configuration as Config ;


    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{
            
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Emails', array('requred' => 1));
            $fhandler->addRule('message', 'Message', array('required' => 1));
            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            
            //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            $fvalues = $fhandler->getValues();
           
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }
                
            $loginId = Login::getLoginIdInSession();
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
           
            $emails = explode(",",$fvalues["email"]);
            $message = $fvalues["message"];

            $mailDao = new \com\indigloo\sc\dao\Mail();
            $mailDao->capture($emails,$message);

            $qUrl = base64_decode($fvalues['qUrl']);
            $message = sprintf("success! invitations sent!");
            $gWeb->store(Constants::FORM_MESSAGES,array($message));


            header("Location: ". $qUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
