<?php
    //qa/external/router.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;

    try{

        $gWeb = \com\indigloo\core\Web::getInstance();

        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('images_json', 'images_json', array('rawData' => 1));

        $fvalues = $fhandler->getValues();
        $qUrl = $fvalues['qUrl'];
        $fUrl = $fvalues['fUrl'];

        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        // route to new form page
        // put images_json in sticky
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        header("Location: "."/share/new.php");


    } catch(UIException $ex) {
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
        header("Location: " . $fUrl);
        exit(1);
    }

?>
