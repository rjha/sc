<?php
    //qa/external/form/next.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    //@todo include user role page.

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['next']) && ($_POST['next'] == 'Next')) {
        try{

            $gWeb = \com\indigloo\core\Web::getInstance();

            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('images_json', 'images_json', array('rawData' => 1));

            $fvalues = $fhandler->getValues();
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }
            print_r($fvalues);
            exit ;


        } catch(UIException $ex) {
            //@todo error handling
            exit(1);
        }

    }
?>
