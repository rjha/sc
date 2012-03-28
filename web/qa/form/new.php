<?php
    //qa/form/new.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);

        $fhandler->addRule('links_json', 'links_json', array('noprocess' => 1));
		$fhandler->addRule('images_json', 'images_json', array('noprocess' => 1));
		$fhandler->addRule('group_names', 'Tags', array('maxlength' => 64));
		
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();

		$qUrl = $fvalues['q'];
    
        
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            
            header("Location: " . $qUrl);
            exit(1);
			
        } else {
           
            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names =$fvalues['group_names']  ;
            $group_slug = $groupDao->nameToSlug($group_names);

            $postDao = new com\indigloo\sc\dao\Post();
			$title = Util::abbreviate($fvalues['description'],128);		

            $data = $postDao->create(
								$title,
                                $fvalues['description'],
								$gSessionLogin->id,
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug,
                                $fvalues['category']);
								
   			$code = $data['code'];

            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
				$location = "/item/".$data['itemId'];
                header("Location: /qa/thanks.php?q=".$location );
                
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
