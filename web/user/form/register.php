<?php
    //sc/user/form/register.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	include($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/recaptchalib.php');
    require_once($_SERVER['WEBGLOO_LIB_ROOT']. '/ext/sendgrid-php/SendGrid_loader.php');
    
    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
    
    if (isset($_POST['register']) && ($_POST['register'] == 'Register')) {
        try{ 
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));
        
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();
            $qUrl = '/user/register.php' ;
        
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
                throw new UIException($fhandler->getErrors(),1);
            }
         
            $userName = $fvalues['first_name']. ' '.$fvalues['last_name'];
			$provider = \com\indigloo\sc\auth\Login::MIK ;

            //create a new login 
            $loginDao = new \com\indigloo\sc\dao\Login();
            $data = $loginDao->create($provider,$userName);
            $code = $data['code'];
            if($code != 0 ) {
                $message = "DB Error : code %d ";
                $message = sprintf($message,$code);
                throw new DBException($messge,$code);
            }

            $loginId = $data['lastInsertId'];

            if(is_null($loginId)){
                $messages = array("Null login Id in registration");
				throw new UIException($messages,1);
			}

            $data = \com\indigloo\auth\User::create('sc_user',
								$fvalues['first_name'],
                                $fvalues['last_name'],
								$userName,
                                $fvalues['email'],
								$fvalues['password'],
								$loginId);
    
            $code = $data['code'];
            if($code != 0 ) {
                $message = "DB Error : code %d ";
                $message = sprintf($message,$code);
                throw new DBException($message,$code);
            }

            //success
            $gWeb->store(Constants::FORM_MESSAGES,array("Registration success! Please login."));
            header("Location: /user/login.php");

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $qUrl);
            exit(1);
        } catch(DBException $dbex) {
            $message = $dbex->getMessage();
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,array($message));
            header("Location: " . $qUrl);
            exit(1);
        }

    }
?>
