<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\sc\Constants as AppConstants ;

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

    switch($action) {
        case UIConstants::FEATURE_POST :
            //set:key, member, source 
            $collectionDao->sadd(AppConstants::SYS_FP_SET,$postId,AppConstants::ITEM);
            $message = sprintf("success! %s %s added to set %s",
                AppConstants::ITEM,$postId,AppConstants::SYS_FP_SET);
            break ;
        case UIConstants::UNFEATURE_POST :
            $collectionDao->srem(AppConstants::SYS_FP_SET,$postId);
            break ;
        default:
            trigger_error("Unknown UI action", E_USER_ERROR);
    }
    
    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
?>
