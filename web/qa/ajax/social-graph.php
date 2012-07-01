<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    set_exception_handler('webgloo_ajax_exception_handler');

    //use login is required for bookmarking
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $params = new \stdClass;
    $login = Login::getLoginInSession();
    $params->loginId = $login->id ;
    $params->name = $login->name ;
    
    $params->action = Util::tryArrayKey($_POST, "action");
    $params->followerId = Util::tryArrayKey($_POST, "followerId");
    $params->followingId = Util::tryArrayKey($_POST, "followingId");

    $command = new \com\indigloo\sc\command\SocialGraph();
    $response = $command->execute($params);
    $html = json_encode($response);
    echo $html;
?>
