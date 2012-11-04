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
            
            $fhandler->addRule("qUrl", "go back to URL", array('required' => 1, 'rawData' =>1));
            $fhandler->addRule("list_id", "list id", array('required' => 1));

            $fvalues = $fhandler->getValues();
            $gWeb = \com\indigloo\core\Web::getInstance();

            $qUrl = base64_decode($fvalues["qUrl"]);
            $listId = $fvalues["list_id"];
             
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            $loginId = Login::getLoginIdInSession();
            $listDao = new \com\indigloo\sc\dao\Lists();
            $listDao->edit(
                $loginId,
                $fvalues["list_id"],
                $fvalues["name"],
                $fvalues["description"]);

            $message = sprintf("success! list updated");
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
        

    }

?>