<?php
    //qa/form/new.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\exception\UIException as UIException;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{
            
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('links_json', 'links_json', array('rawData' => 1));
            $fhandler->addRule('images_json', 'images_json', array('rawData' => 1));
            $fhandler->addRule('group_names', 'Tags', array('maxlength' => 64,'rawData' => 1));

            //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            $fvalues = $fhandler->getValues();
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names =$fvalues['group_names']  ;
            $group_slug = $groupDao->nameToSlug($group_names);

            $postDao = new com\indigloo\sc\dao\Post();
            $title = Util::abbreviate($fvalues['description'],128);

            $itemId = $postDao->create($title,
                                $fvalues['description'],
                                $gSessionLogin->id,
                                $gSessionLogin->name,
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug,
                                $fvalues['category']);

            //success - always go to item details
            $location = "/item/".$itemId;
            header("Location: /qa/thanks.php?q=".$location );

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
