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


    //parameters
    $loginId = Login::getLoginIdInSession();
    $postId = Util::getArrayKey($_POST, "postId");
    $action = Util::getArrayKey($_POST, "action");

    $map = array();
    $map["LIKE"] = 1 ;
    $map["SAVE"] = 2 ;

    $code = $map[$action];

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
    $bookmarkDao->add($loginId,$postId,$code);
    $html = array("code" => 200 , "message" => "success");
    $html = json_encode($html); 
    echo $html;
?>
