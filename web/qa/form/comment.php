<?php
    //qa/form/comment.php

    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;

    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\exception\UIException as UIException;

    

    if (isset($_POST['save']) && ($_POST['save'] == 'Save')) {

        $gWeb = \com\indigloo\core\Web::getInstance(); 
        $fvalues = array();
        $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

       

        try{

            $fhandler = new Form\Handler('web-form-1', $_POST);

            $fhandler->addRule('comment', 'Comment', array('required' => 1));
            $fhandler->addRule('post_id', 'post id', array('required' => 1));
            $fhandler->addRule('owner_id', 'owner id', array('required' => 1));
            $fhandler->addRule('post_title', 'post title', array('required' => 1));
            
            $fvalues = $fhandler->getValues();
            
            // UI checks
            if ($fhandler->hasErrors()) {
                throw new UIException($fhandler->getErrors());
            }

            //trim comments to 512 chars
            $fvalues["comment"] = substr($fvalues["comment"],0,512);
            
            //use login is required for comments
            if(Login::hasSession()) {

                $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
                $commentDao = new com\indigloo\sc\dao\Comment();
                $commentDao->create($gSessionLogin->id,
                                        $gSessionLogin->name,
                                        $fvalues['owner_id'],
                                        $fvalues['post_id'],
                                        $fvalues['post_title'],
                                        $fvalues['comment']);

                // go back to comment form
                header("Location: " . $fUrl);

            } else {
                
                //create data object representing pending session action
                $actionObj = new \stdClass ;
                $actionObj->endPoint = "/qa/form/comment.php" ;

                $params = new \stdClass ;
               
                $params->ownerId = $fvalues['owner_id'];
                $params->postId = $fvalues['post_id'];
                $params->title =  $fvalues['post_title'];
                $params->comment = $fvalues['comment'] ;
                $params->action = UIConstants::ADD_COMMENT ;

                $actionObj->params = $params ;

                //base64 encode to transfer as payload in URL
                $gSessionAction = base64_encode(json_encode($actionObj));
                //encode again for user login page
                $fwd = "/user/login.php?q=".base64_encode($fUrl)."&g_session_action=".$gSessionAction;
                
                header("Location: ".$fwd);
                exit ;
            }

          

        } catch(UIException $ex) {
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());
            header("Location: " . $fUrl);
            exit(1);

        } catch(\Exception $ex) {
            Logger::getInstance()->error($ex->getMessage());
            Logger::getInstance()->backtrace($ex->getTrace());
            $gWeb->store(Constants::STICKY_MAP, $fvalues);
            $message = "Error: looks bad. something went wrong!" ;
            $gWeb->store(Constants::FORM_ERRORS, array($message));
            header("Location: " . $fUrl);
            exit(1);

        }

    }
?>
