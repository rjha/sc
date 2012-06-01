<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    set_error_handler('webgloo_ajax_error_handler');

    //use login is required for bookmarking
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $followerId = Util::tryArrayKey($_POST, "followerId");
    $followingId = Util::tryArrayKey($_POST, "followingId");

    if(empty($followerId) || empty($followingId)) {
        $message = array("code" => 500 , "message" => "Bad input: missing follower or following id!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $followingDBRow = $userDao->getOnLoginId($followingId);
    $followingName = $followingDBRow['name'];

    $followerDBRow = $userDao->getOnLoginId($followerId);
    $followerName = $followerDBRow['name'];

    $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
    $socialGraphDao->addFollower($followerId,$followerName,$followingId,$followingName);
    
    $message = sprintf(" success! You are now following %s ",$followingName);
    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
?>
