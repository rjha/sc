<?php
    //qa/form/comment.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\exception\UIException as UIException;
    use com\indigloo\exception\DBException as DBException;
    
    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {
        try{	
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('comment', 'Comment', array('required' => 1));
            
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();
            $qUrl = $_POST['q'];
            $gWeb = \com\indigloo\core\Web::getInstance();

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            $commentDao = new com\indigloo\sc\dao\Comment();
            $code = $commentDao->create( $fvalues['post_id'],$fvalues['comment'],$gSessionLogin->id);
            if($code != 0 ) {
                $message = "DB Error : code %d ";
                $message = sprintf($message,$code);
                throw new DBException($message,$code);
            }
 
            //success
            header("Location: " . $qUrl);

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
