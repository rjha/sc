<?php
    //monitor/form/post/action.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);
        $fhandler->addRule('ids', 'Post IDS', array('required' => 1));
        $fhandler->addRule('action', 'Action', array('required' => 1));
		
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
		$qUrl = $fvalues['q'];
    
        
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            header("Location: " . $qUrl);
            exit(1);
			
        } else {
            
            $action = $fvalues['action'];
            $strIds = $fvalues['ids'];
            $ids = explode(",",$strIds);
            $dbIds = array();

            foreach($ids as $id) {
                if(Util::tryEmpty($id)) {
                    continue;
                }
                array_push($dbIds,PseudoId::decode($id));
            }

            $strDBIds = implode(",",$dbIds);

            $questionDao = new com\indigloo\sc\dao\Question();
            $data = $questionDao->doAdminAction($strDBIds,$action);
   			$code = $data['code'];

            if ($code == \com\indigloo\mysql\Connection::ACK_OK ) {
                $gWeb->store(Constants::FORM_MESSAGES,array("Action $action is successful!"));
                header("Location: ".$qUrl );
                
            } else {
                $message = sprintf("DB Error: (code is %d) please try again!",$code);
                $gWeb->store(Constants::STICKY_MAP, $fvalues);
                $gWeb->store(Constants::FORM_ERRORS,array($message));
                header("Location: " . $qUrl);
                exit(1);
            }
           
        }
        
    }
?>
