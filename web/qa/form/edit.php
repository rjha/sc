<?php
    //qa/form/edit.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;


    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        try{
            $fhandler = new Form\Handler('web-form-1', $_POST);

            $fhandler->addRule('links_json', 'links_json', array('rawData' => 1));
            $fhandler->addRule('images_json', 'images_json', array('rawData' => 1));
            $fhandler->addRule('group_names', 'Tags', array('maxlength' => 64));
            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));

            $fvalues = $fhandler->getValues();
            
            $qUrl = $fvalues['qUrl'];
            $fUrl = $fvalues['fUrl'];
            $gWeb = \com\indigloo\core\Web::getInstance();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names =$fvalues['group_names']  ;
            $group_slug = $groupDao->nameToSlug($group_names);

            $postDao = new com\indigloo\sc\dao\Post();
            $title = Util::abbreviate($fvalues['description'],128);
            $postDao->update(
                                $fvalues['post_id'],
                                $title,
                                $fvalues['description'],
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug,
                                $fvalues['category']);

            //success
            header("Location: " . $qUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
