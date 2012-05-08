<?php 
    header('Content-type: application/json'); 
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    set_error_handler('webgloo_ajax_error_handler');

    //use login is required for bookmarking
	if(!Login::hasSession()) {
        $html = array("code" => 401 , "message" => "login is required");
        $html = json_encode($html); 
        echo $html;
        exit;
    }
    
    $followerId = Util::getArrayKey($_POST, "followerId");
    $followingId = Util::getArrayKey($_POST, "followingId");

    
    $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
    $socialGraphDao->addFollower($followerId,$followingId);
    $html = array("code" => 200 , "message" => "success");
    $html = json_encode($html); 
    echo $html;
?>