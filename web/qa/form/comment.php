<?php
    //qa/form/comment.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        try{
            $fhandler = new Form\Handler('web-form-1', $_POST);

            $fhandler->addRule('comment', 'Comment', array('required' => 1));
            $fhandler->addRule('post_id', 'post id', array('required' => 1));
            $fhandler->addRule('owner_id', 'owner id', array('required' => 1));
            $fhandler->addRule('post_title', 'post title', array('required' => 1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));

            $fvalues = $fhandler->getValues();
            //redirect always happens to item details page.
            $fUrl = $fvalues['fUrl'];

            $gWeb = \com\indigloo\core\Web::getInstance();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            $commentDao = new com\indigloo\sc\dao\Comment();
            $commentDao->create($gSessionLogin->id,
                                        $gSessionLogin->name,
                                        $fvalues['owner_id'],
                                        $fvalues['post_id'],
                                        $fvalues['post_title'],
                                        $fvalues['comment']);

            //success | error - always go back to form
            header("Location: " . $fUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
