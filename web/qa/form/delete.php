<?php
    //sc/qa/form/delete.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
	
    if (isset($_POST['delete']) && ($_POST['delete'] == 'Delete')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);
        
		$fhandler->addRule('question_id', 'question_id', array('required' => 1));
		$fhandler->addRule('q', 'q', array('required' => 1));
		
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
		$qUrl = $fvalues['q'];
		
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            header("location: /qa/delete.php?id=".$fvalues['question_id']);
            exit(1);
			
        } else {
            
            $questionDao = new com\indigloo\sc\dao\Question();
            $code = $questionDao->delete($fvalues['question_id']);

            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
                header("location: " . $qUrl);
                
            } else {
                $message = sprintf("DB Error: (code is %d) please try again!",$code);
                $gWeb->store(Constants::STICKY_MAP, $fvalues);
                $gWeb->store(Constants::FORM_ERRORS,array($message));
				header("location: /qa/delete.php?id=".$fvalues['question_id']);
                exit(1);
            }
           
        }
    }
?>
