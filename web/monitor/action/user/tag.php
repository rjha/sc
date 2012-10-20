<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    use \com\indigloo\sc\util\Nest as Nest ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\exception\DBException as DBException;

    set_exception_handler("webgloo_ajax_exception_handler");

    //Admin login is required
    if(!Login::isAdmin()) {
        $message = array("code" => 401 , "message" => "Authentication failure! Admin credentials missing.");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $action = Util::getArrayKey($_POST, "action");

    $userDao = new \com\indigloo\sc\dao\User();
    $message = NULL ;

    try{ 
        switch($action) {
            case UIConstants::BAN_USER :
                $loginId = Util::getArrayKey($_POST, "loginId");
                $userDao->ban($loginId);
                $message = sprintf("success! user %s has been banned!",$loginId);
            break ;
            case UIConstants::TAINT_USER :
                $userId = Util::getArrayKey($_POST, "userId");
                $userDao->taint($userId);
                $message = sprintf("success! user %s has been tainted!",$userId);
            break ;
            case UIConstants::UNBAN_USER :
                $loginId = Util::getArrayKey($_POST, "loginId");
                $userDao->unban($loginId);
                $message = sprintf("success! user %s has been restored!",$loginId);
            break ;
            default:
                trigger_error("Unknown UI action", E_USER_ERROR);
        }

    } catch(DBException $ex) {
        //duplicate entry?
        if($ex->getCode() == AppConstants::DUPKEY_ERROR_CODE) {
            $html = array("code" => 500 , "message" => "Duplicate error: member is already in set!");
            $html = json_encode($html);
            echo $html;
            exit ;
        } else {
            throw $ex ;
        }
        
    }
    
    //data saved
    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
    exit ;
?>
