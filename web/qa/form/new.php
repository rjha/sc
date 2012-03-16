<?php
    //qa/form/new.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
	if(is_null($gSessionLogin)) {
		$gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
	}

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        
        $fhandler = new Form\Handler('web-form-1', $_POST);

        $fhandler->addRule('links_json', 'links_json', array('noprocess' => 1));
		$fhandler->addRule('images_json', 'images_json', array('noprocess' => 1));
		
        $fvalues = $fhandler->getValues();
        $ferrors = $fhandler->getErrors();

		$qUrl = $fvalues['q'];
    
        
        if ($fhandler->hasErrors()) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$fhandler->getErrors());
            
            header("Location: " . $qUrl);
            exit(1);
			
        } else {
            
            /* 
             * we first convert all (new) names to array of slugs. we take this array
             * and implode on space to make slugs to be stored in DB (we need to implode on space to
             * index the field via sphinx.
             *
             */

            $group_names =$fvalues['group_names']  ;
            $group_slug = '' ;

            if(!Util::tryEmpty($group_names)) {
                $slugs = array();
                $names = explode(",",$group_names);

                foreach($names as $name) {
                    if(Util::tryEmpty($name)) { continue ; }
                    //make slug from name
                    $slug = \com\indigloo\util\StringUtil::convertNameToKey($name);
                    array_push($slugs,$slug);
                }
                //now arrange slugs
                $group_slug = implode(Constants::SPACE,$slugs);
            }

            $postDao = new com\indigloo\sc\dao\Post();
			$title = Util::abbreviate($fvalues['description'],128);		

            $data = $postDao->create(
								$title,
                                $fvalues['description'],
								$gSessionLogin->id,
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug);
								
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
