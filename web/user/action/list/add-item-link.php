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
    use \com\indigloo\Logger as Logger ;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        try{
            
            $fhandler = new Form\Handler("edit-form", $_POST);
            
            $fhandler->addRule("fUrl", "go back to URL", array('required' => 1, 'rawData' =>1));
            $fhandler->addRule("link", "item URL", array('required' => 1));
            $fhandler->addRule("list_id", "list id", array('required' => 1));


            $fvalues = $fhandler->getValues();
            $gWeb = \com\indigloo\core\Web::getInstance();

            $fUrl = $fvalues["fUrl"]);
            $link = $fvalues["link"];

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $loginId = Login::getLoginIdInSession();
            $listDao = new \com\indigloo\sc\dao\Lists();

            //get itemId from URL
            $path = parse_url($link, \PHP_URL_PATH);
            //path should be /item/<itemno>
            $itemId = substr($path,6);
            /*
            if(!is_int($itemId)) {
                $message = sprintf(" invalid item URL : itemId is : %s",$itemId);
                throw new UIException(array($message));
            } */

            //@todo check if we have a valid item

            $listDao->addItem($fvalues["list_id"],$itemId) ;
            $message = sprintf("success! item added to list ");
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
        

    }

?>
