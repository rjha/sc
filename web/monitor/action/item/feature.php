<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
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


    $postId = Util::getArrayKey($_POST, "postId");
    //Action from UI is ADD | REMOVE
    // see com\indigloo\sc\ui\Constants file
    $action = Util::getArrayKey($_POST, "action");

    $collectionDao = new \com\indigloo\sc\dao\Collection();
    $message = NULL ;

    try{ 
        switch($action) {
            case UIConstants::FEATURE_POST :
                //set:key, member, source 
                $collectionDao->sadd(AppConstants::SYS_FP_SET,$postId,AppConstants::ITEM);
                $message = sprintf("success! %s %s added to featured posts",AppConstants::ITEM,$postId);
                break ;
            case UIConstants::UNFEATURE_POST :
                $collectionDao->srem(AppConstants::SYS_FP_SET,$postId);
                $message = sprintf("success! %s %s removed from featured posts",AppConstants::ITEM,$postId);
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
