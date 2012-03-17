<?php
    //monitor/form/feature/group.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);
		
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();
		$qUrl = $fvalues['q'];
    
        
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            header("Location: " . $qUrl);
            exit(1);
			
        } else {
            
            $group_slug = '' ;
            $slugs = Util::tryArrayKey($fvalues,'g'); 

            if(!is_null($slugs)) {
                //input - new groups are names / old ones are slugs
                $slugs = array_map(array("\com\indigloo\util\StringUtil","convertNameToKey"),$slugs);
                //db slugs are space separated for sphinx indexing
                $group_slug = implode(Constants::SPACE,$slugs);
            }

            $groupDao = new \com\indigloo\sc\dao\Group();
            $code = $groupDao->setFeature($group_slug);

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
