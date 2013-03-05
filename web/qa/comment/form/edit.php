<?php
    //qa/comment/form/edit.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{

            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('comment', 'Comment', array('required' => 1));
            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();

            //decode to use in redirect
            $qUrl = base64_decode($fvalues['qUrl']);
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $commentDao = new com\indigloo\sc\dao\Comment();
            $commentDao->update($fvalues['comment_id'], $fvalues['comment']);

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
