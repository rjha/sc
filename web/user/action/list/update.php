<?php
    
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;

    // @imp submit buttons are only considered successful controls 
    // if they are used to submit the form
    // this form can be submitted using javascript also so do not include
    // submit_button_in_$_POST check.

    try{
        
        $fhandler = new Form\Handler("list-form-1", $_POST);
        //input rules
        $fhandler->addRule("qUrl", "qUrl", array('required' => 1, 'rawData' =>1));
        $fhandler->addRule("item_id", 'item', array('required' => 1));
        
        $fvalues = $fhandler->getValues();

        //qUrl is needed for redirect
        $gWeb = \com\indigloo\core\Web::getInstance();
        $qUrl = base64_decode($fvalues["qUrl"]);
          
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $listId = $fvalues["list_id"] ;
        $itemId = $fvalues["item_id"];
        $loginId = Login::getLoginIdInSession(); 
        $flag = intval($fvalues["is_new"]);

        $listDao = new \com\indigloo\sc\dao\Lists();

        if( ($flag == 1) && empty($listId)) {
            // create new list 
            $name =  $fvalues["new-list-name"];
            if(!Util::isAlphaNumeric($name)) {
                $error = "Bad name : only letters and numbers are allowed!" ;
                throw new UIException(array($error));
            }

            $listDao->create($loginId,$name,$itemId);

        } else {
            //Add to existing list 
            $listDao->addItem($listId,$itemId);
        }

        $message = sprintf("success! items added to list");
        $gWeb->store(Constants::FORM_MESSAGES,array($message));

        header("Location: " . $qUrl);


    }catch(UIException $ex) {
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
        header("Location: " . $qUrl);
        exit(1);

    }catch(DBException $ex) {
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = "Error: something went wrong with database operation" ;
        $gWeb->store(Constants::FORM_ERRORS,array($message));
        header("Location: " . $qUrl);
        exit(1);

    }catch(\Exception $ex) {
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = "Error: looks bad. something went wrong!" ;
        $gWeb->store(Constants::FORM_ERRORS, array($message));
        header("Location: " . $qUrl);
        exit(1);

    }
    

?>
