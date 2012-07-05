<?php
    //sc/user/form/mails-preference.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\Util as Util ;

    if (isset($_POST["save"]) && ($_POST["save"] == "Save")) {
        try{
            $fhandler = new Form\Handler("web-form-1", $_POST);
            $fvalues = $fhandler->getValues();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $fUrl = $fvalues["fUrl"];

            $gWeb = \com\indigloo\core\Web::getInstance();
            $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
            $loginId = $gSessionLogin->id;

            // p array would be missing when no checkbox is ticked.
            // that also happens when user de-selects everything
            // for user selections - p array would contain only those
            // keys that user has selected. we have to map the rest of them
            // to false in preferences data.
            $parr = Util::tryArrayKey($fvalues,"p");
            $pdata = array();

            if(is_null($parr)) {
                //user has not selected any checckbox.
                $pdata = array("follow" => false, "comment" => false, "bookmark" => false);
            }else {
                // p array is not empty.
                // user has ticked some checkboxes.
                // set to false the keys that user has not selected.

                $pdata["follow"] = isset($parr["follow"]) ? true : false ;
                $pdata["comment"] = isset($parr["comment"]) ? true : false ;
                $pdata["bookmark"] = isset($parr["bookmark"]) ? true : false ;
            }

            //save data for this loginId
            $pDataObj = json_encode($pdata);
            $preferenceDao = new \com\indigloo\sc\dao\Preference();
            $preferenceDao->update($loginId,$pDataObj);


            $gWeb->store(Constants::FORM_MESSAGES,array("Your settings have been updated."));

            //set success message
            header("Location: ".$fUrl);

        }catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }

    }
?>
