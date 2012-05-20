<?php
    //sc/user/account/form/change-password.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    //This form is also used by mail reset workflow 
    //so do not add role/user.inc here
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\auth\User as WebglooUser ;
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

	    try{	
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));

            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();

            $qUrl = $fvalues['qUrl'];
            $fUrl = $fvalues['fUrl'];
            $gWeb = \com\indigloo\core\Web::getInstance();
    
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            //form token
            
            $session_token = $gWeb->find("change.password.token",true);
            if($fvalues['ftoken'] != $session_token) {
                $message = "form token does not match the value stored in session";
                throw new UIException(array($message),1);
            } 

            //decrypt email
            $email = $gWeb->find("change.password.email",true);
            $email = Util::decrypt($email);

            $userDao = new \com\indigloo\sc\dao\User();
            //@test with email that can cause issues with encoding!
            $userDBRow = $userDao->getOnEmail($email);

            //send raw password
            $email = strtolower(trim($email));
            $password = trim($_POST['password']);
            $code = WebglooUser::changePassword('sc_user',$userDBRow['login_id'],$email,$password) ;
			
            if($code != \com\indigloo\mysql\Connection::ACK_OK ) {
                $message = sprintf("DB Error : code %d \n",$code);
                throw new DBException($message,1);
            }

            //success
            header("Location: " . $qUrl);
            exit(1);

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
