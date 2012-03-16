<?php
    //qa/form/edit.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\util\StringUtil as StringUtil ;
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

            $group_names =$fvalues['group_names']  ;
            $group_slug = '' ;

            if(!Util::tryEmpty($group_names)) {
                $slugs = array();
                $names = explode(",",$group_names);

                foreach($names as $name) {
                    if(Util::tryEmpty($name)) { continue ; }
                    $slug = \com\indigloo\util\StringUtil::convertNameToKey($name);
                    array_push($slugs,$slug);
                }

                $group_slug = implode(Constants::SPACE,$slugs);
            }

 
            $postDao = new com\indigloo\sc\dao\Post();
			$title = Util::abbreviate($fvalues['description'],128);		
            $code = $postDao->update(
								$fvalues['post_id'],
								$title,
                                $fvalues['description'],
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug);
            
            if ($code == com\indigloo\mysql\Connection::ACK_OK ) {
                $itemId = PseudoId::encode($fvalues['post_id']);
                $locationOnSuccess = "/item/".$itemId ;
                header("Location: " . $locationOnSuccess);
                
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
