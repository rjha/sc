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

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");


        try{
            
            $fhandler = new Form\Handler("edit-form", $_POST);
            $fhandler->addRule("list_id", "list id", array('required' => 1));

            $fvalues = $fhandler->getValues();
            $name = $fvalues["name"];
            
            if(!Util::isAlphaNumeric($name)) {
                $fhandler->addError("Bad name : only letters and numbers are allowed!") ;
            }

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $listId = $fvalues["list_id"];
            
            $loginId = Login::getLoginIdInSession();
            $listDao = new \com\indigloo\sc\dao\Lists();
            $listDao->edit(
                $loginId,
                $fvalues["list_id"],
                $name,
                $fvalues["description"]);

            $message = sprintf("success! list updated");
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
            if($ex->getCode() == 23000) {
                $message =  sprintf("Error: list name _%s_ is already in use!",$name);
            }

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
