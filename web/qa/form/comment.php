<?php
    //qa/form/comment.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
	if(is_null($gSessionLogin)) {
		$gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
	}

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
		
        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('comment', 'Comment', array('required' => 1));
        
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
    
        
        if ($fhandler->hasErrors()) {
            $locationOnError = $_POST['q'] ;
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            
            header("Location: " . $locationOnError);
            exit(1);
			
        } else {
            
            $commentDao = new com\indigloo\sc\dao\Comment();
			
            $code = $commentDao->create( $fvalues['post_id'],$fvalues['comment'],$gSessionLogin->id);
            
            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
                $locationOnSuccess = $_POST['q'];
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
