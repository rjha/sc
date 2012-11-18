<?php
    //sc/qa/form/delete.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\sc\util\PseudoId ;

    if (isset($_POST['delete']) && ($_POST['delete'] == 'Delete')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");


        try{
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('post_id', 'post_id', array('required' => 1));
            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();

            // q is part of URL and base64 encoded
            // we need to decode it to use it for redirection
            $qUrl = base64_decode($fvalues['qUrl']);
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $postDao = new com\indigloo\sc\dao\Post();
            $postDao->delete($fvalues['post_id']);

            //success
            header("location: " . $qUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
