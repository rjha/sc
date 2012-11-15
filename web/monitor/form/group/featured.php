<?php
    //monitor/form/group/featured.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\sc\util\Nest as Nest;

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        try {
            
            $fhandler = new Form\Handler("web-form-1", $_POST);
            $fhandler->addRule("fUrl", "go back to URL", array('required' => 1, 'rawData' =>1));

            $fvalues = $fhandler->getValues();
            $fUrl = $fvalues["fUrl"];
            $gWeb = \com\indigloo\core\Web::getInstance();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }


            $group_slug = "" ;
            $slugs = Util::tryArrayKey($fvalues,"g");

            if(!is_null($slugs)) {

                //remove duplicate entries
                $slugs = array_unique($slugs);
                //input - new groups are names / old ones are slugs
                $slugs = array_map(array("\com\indigloo\util\StringUtil","convertNameToKey"),$slugs);
                //db slugs are space separated for sphinx indexing
                $group_slug = implode(Constants::SPACE,$slugs);

            }

            $collectionDao = new \com\indigloo\sc\dao\Collection();
            $collectionDao->glset(Nest::fgroups(),$group_slug);

            //success
            $gWeb->store(Constants::FORM_MESSAGES,array("featured groups list updated!"));
            header("Location: ".$fUrl );

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
