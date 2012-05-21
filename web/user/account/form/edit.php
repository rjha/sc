<?php
    //sc/user/account/form/edit.php
    
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\sc\auth\Login as Login ;

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        try {
            $fhandler = new Form\Handler('web-form-1', $_POST);
            $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('last_name', 'Last Name', array('required' => 1, 'maxlength' => 32));
            $fhandler->addRule('email', 'Email', array('required' => 1, 'maxlength' => 64));

            $fhandler->addRule('qUrl', 'qUrl', array('required' => 1, 'rawData' =>1));
            $fhandler->addRule('fUrl', 'fUrl', array('required' => 1, 'rawData' =>1));
            
            $fvalues = $fhandler->getValues();
            $ferrors = $fhandler->getErrors();

            $qUrl = $fvalues['qUrl'];
            $fUrl = $fvalues['fUrl'];

            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors(),1);
            }

            $loginId = Login::getLoginIdInSession();
            $userDao = new \com\indigloo\sc\dao\User();
            $code = $userDao->update($loginId,
                $fvalues['first_name'], 
                $fvalues['last_name'],
                $fvalues['nick_name'],
                $fvalues['email'],
                $fvalues['website'],
                $fvalues['blog'],
                $fvalues['location'],
                $fvalues['age'],
                $fvalues['photo_url']) ;

            if($code != 0 ) {
                $message = "DB Error : code %d ";
                $message = sprintf($message,$code);
                throw new DBException($message,$code);
            }
            
            //success
            header("Location: ".$qUrl);

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);
        } catch(DBException $dbex) {
            $message = $dbex->getMessage();
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,array($message));
            header("Location: " . $fUrl);
            exit(1);
        }

   }
?>
