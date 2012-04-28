<?php
    //sc/user/profile/form/edit.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');


    
    use com\indigloo\ui\form as Form;
    use com\indigloo\Constants as Constants ;
	$mikUser = NULL ;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
		
        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('password', 'Password', array('required' => 1 , 'maxlength' => 32));
        
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
    
        
        if ($fhandler->hasErrors()) {
            $locationOnError = '/user/profile/password.php' ;
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            
            header("Location: " . $locationOnError);
            exit(1);
        } else {
		
			if(is_null($mikUser)) {
				$mikUser = \com\indigloo\auth\User::getUserInSession();
			}
			
            //send raw password - w/o any processing
			$data = \com\indigloo\auth\User::changePassword('sc_user',$mikUser->id,$mikUser->email,$_POST['password']) ;
            
			$code = $data['code'];
			
            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
                header("Location: /user/dashboard.php ");

            }else {
                $message = sprintf("DB Error: (code is %d) please try again!",$code);
                $gWeb->store(Constants::STICKY_MAP, $fvalues);
                $gWeb->store(Constants::FORM_ERRORS,array($message));
                $locationOnError = '/user/profile/password.php' ;
                header("Location: " . $locationOnError);
                exit(1);
            }
            
        }
    }
?>
