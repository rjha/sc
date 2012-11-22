<?php
    //sc/user/form/register.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(WEBGLOO_LIB_ROOT . '/ext/recaptchalib.php');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Logger ;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\sc\auth\Login as Login ;

    if (isset($_POST['register']) && ($_POST['register'] == 'Register')) {

        $gWeb = \com\indigloo\core\Web::getInstance();
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

        try{

            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));
            
             //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            $fvalues = $fhandler->getValues();
            
            if(!empty($fvalues["adrisya_number"])) {
                $message = "unexpected error with form submission!" ;
                $fhandler->addError($message) ;
                $error = "Possible spam bot submission from IP :: ". $_SERVER["REMOTE_ADDR"]; 
                Logger::getInstance()->info($error);
            }
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            //create a new login + user
            $loginDao = new \com\indigloo\sc\dao\Login();
            $loginDao->create($fvalues['first_name'],
                                $fvalues['last_name'],
                                $fvalues['email'],
                                $fvalues['password']);


            //canonical email - all lower case
            $email = strtolower(trim($fvalues['email']));
            $password = trim($fvalues['password']);
            $loginId = NULL ;

            try{
                $loginId = \com\indigloo\auth\User::login('sc_user',$email,$password);
            } catch(\Exception $ex) {
                $code = $ex->getCode();
                switch($code) {
                    case 401 :
                        $message = "Wrong login or password. Please try again!";
                        throw new UIException(array($message));
                    break ;
                    default:
                        $message = "Error during login. Please try after some time!";
                        throw new UIException(array($message));
                }
            }

            //success - update login record
            // start 3mik session
            $remoteIp = \com\indigloo\Url::getRemoteIp();
            mysql\Login::updateIp(session_id(),$loginId,$remoteIp);
            Login::startOAuth2Session($loginId,Login::MIK);

            //add overlay message
            $message = "success! Thanks for joining ".$fvalues['first_name'];
            $gWeb->store("global.overlay.message", $message);
            header("Location: /user/dashboard/index.php");

            exit ;


        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        } catch(DBException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            // @imp: this is mysql error code
            // @todo need to define this as a constant
            if($ex->getCode() == 1062 ) {
                $message = sprintf("Email %s is already registered with us.", $fvalues['email']);
            } else {
                $message = $ex->getMessage();
            }

            $gWeb->store(Constants::FORM_ERRORS,array($message));
            header("Location: " . $fUrl);
            exit(1);

        }

    }
?>
