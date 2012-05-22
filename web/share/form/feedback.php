<?php
    //sc/share/form/feedback.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        try{    

            $gWeb = \com\indigloo\core\Web::getInstance();
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('feedback', 'Feedback', array('required' => 1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));

            //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();

            $fUrl = $fvalues['fUrl'];


            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }
            
            $userDao = new com\indigloo\sc\dao\User();
            $code = $userDao->addFeedback($fvalues['feedback']);
            if($code != 0 ) {
                $message = "DB Error : code %d ";
                $message = sprintf($message,$code);
                throw new DBException($message,$code);
            }
 
            //success - always go back to feedback form 
            $gWeb->store(Constants::FORM_MESSAGES,array('Thanks for your feedback.'));
            header("Location: ".$fUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        } catch(DBException $dbex) {
            $message = $dbex->getMessage();
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,array($message));
            header("Location: " . $fUrl);
            exit(1);
        }
    }
?>
