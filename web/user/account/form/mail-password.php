<?php
    //sc/user/account/form/mail-password.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    require_once($_SERVER['WEBGLOO_LIB_ROOT']. '/ext/sendgrid-php/SendGrid_loader.php');

    /* you can also preprend ext/sendgrid-php to APP_LIB_PATH 
     * and get rid of above  inclusion 
        $_SERVER['APP_LIB_PATH'] = array(
            0 => '/home/rjha/code/github/sc/webgloo/lib/ext/sendgrid-php',
            1 => '/home/rjha/code/github/sc/webgloo/lib');
     */

     
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
     
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        try {
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('email', 'Email', array('maxlength' => 64, 'required' =>1));
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();
            $qUrl = $fvalues['q'];
            $gWeb = \com\indigloo\core\Web::getInstance();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            $userDao = new \com\indigloo\sc\dao\User();
            $user = $userDao->getOnEmail($fvalues['email']);

            if(empty($user)) {
                $message = "Error: We did not find any account with this email!";
                throw new UIException(array($message),1);
            }
            
            $mailDao = new \com\indigloo\sc\dao\Mail();
            $mailDao->addResetPassword($user['name'],$fvalues['email']);
            
            $message = "Success! You will receive an email soon!";
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_MESSAGES,array($message));
            header("Location: ".$qUrl);
            exit;

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
