<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    set_exception_handler('webgloo_ajax_exception_handler');

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

    $postDao = new \com\indigloo\sc\dao\Post();
    switch($action) {
        case UIConstants::FEATURE_POST :
            $postDao->doAdminAction($postId, \com\indigloo\sc\Constants::FEATURE_POST);
            break ;
        case UIConstants::UNFEATURE_POST :
            $postDao->doAdminAction($postId,\com\indigloo\sc\Constants::UNFEATURE_POST);
            break ;
        default:
            trigger_error("Unknown UI action", E_USER_ERROR);
    }
    
    $html = array("code" => 200 , "message" => "success");
    $html = json_encode($html);
    echo $html;
?>
