<?php 
    header('Content-type: application/json'); 
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    
    set_error_handler('webgloo_ajax_error_handler');

    //Admin login is required 
    if(!Login::isAdmin()) {
        $message = array("code" => 401 , "message" => "Authentication failure! Admin credentials missing.");
        $html = json_encode($message); 
        echo $html;
        exit;
    }

    //parameters
    $loginId = Login::getLoginIdInSession();
    $postId = Util::getArrayKey($_POST, "postId");
    $action = Util::getArrayKey($_POST, "action");

    $postDao = new \com\indigloo\sc\dao\Post();
    $map = array();
    $map["ADD"] = $postDao::FEATURE_POST ;
    $map["REMOVE"] =  $postDao::UNFEATURE_POST;
    $action2 = $map[$action];
    $postDao->doAdminAction($postId,$action2);

    $html = array("code" => 200 , "message" => "success");
    $html = json_encode($html); 
    echo $html;
?>
