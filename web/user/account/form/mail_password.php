<?php
    //sc/site/wf/password/form/mail.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);

		$fhandler->addRule('email', 'Email', array('maxlength' => 64, 'required' =>1));
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
		$qUrl = $fvalues['q'];
        
        if ($fhandler->hasErrors()) {
            show_error($fvalues,$qUrl,$fhandler->getErrors()) ;
        }

        $userDao = new \com\indigloo\sc\dao\User();
        $user = $userDao->getOnEmail($fvalues['email']);

        if(empty($user)) {
            $message = "Error: We did not find any account with this email!";
            show_error($fvalues,$qUrl,array($message));
        }
            
        $mailDao = new \com\indigloo\sc\dao\Mail();
        $code = $mailDao->addResetPassword($user['user_name'],$user['email']);
        check_db_code($code,$fvalues,$qUrl);

        $message = "Success! You will receive an email soon!";
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_MESSAGES,array($message));
        header("Location: ".$qUrl);
        exit;
        
    }

    function check_db_code($code,$fvalues,$qUrl) {
        $gWeb = \com\indigloo\core\Web::getInstance();
        if ($code != com\indigloo\mysql\Connection::ACK_OK ) {
            $message = sprintf("Error: (DB code is %d) please try again!",$code);
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,array($message));
            header("Location: " . $qUrl);
            exit(1);
        }
    }

    function show_error($fvalues,$qUrl,$errors) {
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$errors);
        header("Location: " . $qUrl);
        exit(1);
    }

?>
