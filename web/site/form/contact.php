<?php
    //sc/site/form/contact.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{
            
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('comment', 'Comment', array('required' => 1, 'maxlength' => 512));
            $fhandler->addRule('name', 'Name', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            
            //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            $fvalues = $fhandler->getValues();
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $feedbackDao = new com\indigloo\sc\dao\Feedback();
            $feedbackDao->add($fvalues['name'],
                                $fvalues['email'],
                                $fvalues['phone'],
                                $fvalues['comment']);

            //success - always go back to feedback form
            $gWeb->store(Constants::FORM_MESSAGES,array('Thanks for your input.'));
            header("Location: ".$fUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }
    }
?>
