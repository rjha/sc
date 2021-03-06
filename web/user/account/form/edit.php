<?php
    //sc/user/account/form/edit.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\auth\Login as Login ;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try {
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));

            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fvalues = $fhandler->getValues();

            //decode q param to use in redirect
            $qUrl = base64_decode($fvalues['qUrl']);
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $loginId = Login::getLoginIdInSession();
            $userDao = new \com\indigloo\sc\dao\User();
            $userDao->update($loginId,
                $fvalues['first_name'],
                $fvalues['last_name'],
                $fvalues['nick_name'],
                $fvalues['email'],
                $fvalues['website'],
                $fvalues['blog'],
                $fvalues['location'],
                $fvalues['age'],
                $fvalues['photo_url'],
                $fvalues['about_me']) ;


            //success
            header("Location: ".$qUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

   }
?>
