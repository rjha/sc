<?php
    //sc/share/form/feedback.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
		
        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('feedback', 'Feedback', array('required' => 1));
        
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
		$locationOnError = "/share/feedback.php";
    
        
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            header("Location: " . $locationOnError);
            exit(1);
			
        } else {
            
            $userDao = new com\indigloo\sc\dao\User();
            $code = $userDao->addFeedback($fvalues['feedback']);
            
            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
                $locationOnSuccess = "/";
                header("Location: " . $locationOnSuccess);
                
            } else {
                $message = sprintf("DB Error: (code is %d) please try again!",$code);
                $gWeb->store(Constants::STICKY_MAP, $fvalues);
                $gWeb->store(Constants::FORM_ERRORS,array($message));
                $locationOnError = $_POST['q'] ;
                header("Location: " . $locationOnError);
                exit(1);
            }
        }
        
    }
?>
