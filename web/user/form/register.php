<?php
    //sc/user/form/register.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	include($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/recaptchalib.php');
     
    
    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    
    if (isset($_POST['register']) && ($_POST['register'] == 'Register')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
        $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
        $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
        $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));
        
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
    
		//captcha code
        $privatekey = "6Lc3p80SAAAAABtSCxk0iHeZDRrMxvC0XTTqJpHI";
        $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
            $fhandler->addError("Wrong answer to Captcha! Please try again!");
        }
		
        
        if ($fhandler->hasErrors()) {
            $locationOnError = '/user/register.php' ;
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            
            header("Location: " . $locationOnError);
            exit(1);
        } else {

            $userName = $fvalues['first_name']. ' '.$fvalues['last_name'];
			$provider = \com\indigloo\sc\auth\Login::MIK ;
			$loginId = create_login($provider,$userName);

			if(is_null($loginId)){
				trigger_error("Null login Id in registration",E_USER_ERROR);
			}

            $data = \com\indigloo\auth\User::create('sc_user',
								$fvalues['first_name'],
                                $fvalues['last_name'],
								$userName,
                                $fvalues['email'],
								$fvalues['password'],
								$loginId);
    
            $code = $data['code'];
            
            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
				$gWeb->store(Constants::FORM_MESSAGES,array("Registration success! Please login."));
				header("Location: /user/login.php");
            }else {
				process_error($code);
			}
            
        }
    }

	function create_login($provider,$name) {
		$loginId = NULL ;
		//create a new login 
		$loginDao = new \com\indigloo\sc\dao\Login();
		$data = $loginDao->create($provider,$name);
		$code = $data['code'];
		if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
			return $data['lastInsertId'];
		} else {
			process_error($code);
		}
	}


	function process_error($code){
		$message = sprintf("DB Error: (code is %d) please try again!",$code);
		$gWeb->store(Constants::STICKY_MAP, $fvalues);
		$gWeb->store(Constants::FORM_ERRORS,array($message));
		$locationOnError = '/user/register.php' ;
		header("Location: " . $locationOnError);
		exit(1);
	}


?>
