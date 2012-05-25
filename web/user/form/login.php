<?php
    //sc/user/form/login.php
    
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    
    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
    
    if (isset($_POST['login']) && ($_POST['login'] == 'Login')) {
        try{ 
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));
            $fhandler->addRule('password', 'Password', array('required' => 1, 'maxlength' => 32));

            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();
            $gWeb = \com\indigloo\core\Web::getInstance();

            $qUrl = $fvalues['qUrl'];
            $fUrl = $fvalues['fUrl'];
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            //canonical email - all lower case
            $email = strtolower(trim($fvalues['email']));
            $password = trim($fvalues['password']);
            $code = \com\indigloo\auth\User::login('sc_user',$email,$password);
            
            if ($code < 0 ) {
                $message = "Wrong login or password. Please try again!";
                throw new UIException(array($message),1);
            }

            //success set our own session variables
            \com\indigloo\sc\auth\Login::startMikSession();
            header("Location: ".$qUrl);

        }catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        }
                
    }
?>
