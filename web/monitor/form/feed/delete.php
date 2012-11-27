<?php
    
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\Logger as Logger ;
    
    use \com\indigloo\sc\util\Nest as Nest;
    use \com\indigloo\sc\redis as redis ;

    // submitting via javascript
    // removed button value check 

    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler("delete-form", $_POST);
        $fhandler->addRule("items", "items", array('required' => 1,'rawData' =>1));

        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $items_json = $fvalues["items"];
        $items = json_decode($items_json);
        $redisObj = new redis\Activity();

        foreach($items as $item) {
            //delete this item from global feed
            $redisObj->lrem(Nest::global_feeds(),$item);
        }

        $message = sprintf("success! selected items have been deleted");
        $gWeb->store(Constants::FORM_MESSAGES,array($message));

        header("Location: " . $fUrl);
        

    }catch(UIException $ex) {
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
        header("Location: " . $fUrl);
        exit(1);

    }catch(DBException $ex) {
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = "Error: something went wrong with database operation" ;
        $gWeb->store(Constants::FORM_ERRORS,array($message));
        
        header("Location: " . $fUrl);
        exit(1);
    }catch(\Exception $ex) {
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = "Error: looks bad. something went wrong!" ;
        $gWeb->store(Constants::FORM_ERRORS, array($message));
        
        header("Location: " . $fUrl);
        exit(1);
    }
    

?>
