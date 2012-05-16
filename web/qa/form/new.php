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
    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
   	 
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        try{
        
            $gWeb = \com\indigloo\core\Web::getInstance();

            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('links_json', 'links_json', array('noprocess' => 1));
            $fhandler->addRule('images_json', 'images_json', array('noprocess' => 1));
            $fhandler->addRule('group_names', 'Tags', array('maxlength' => 64));
            
            //check security token
            $fhandler->checkToken("token",$gWeb->find("form.token",true)) ;
            $fvalues = $fhandler->getValues();

            $qUrl = $fvalues['q'];
            
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            } 

            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names =$fvalues['group_names']  ;
            $group_slug = $groupDao->nameToSlug($group_names);

            $postDao = new com\indigloo\sc\dao\Post();
			$title = Util::abbreviate($fvalues['description'],128);		

            $itemId = $postDao->create($title,
                                $fvalues['description'],
								$gSessionLogin->id,
                                $_POST['links_json'],
                                $_POST['images_json'],
                                $group_slug,
                                $fvalues['category']);
		    
            //success
            $location = "/item/".$itemId;
            header("Location: /qa/thanks.php?q=".$location );

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
