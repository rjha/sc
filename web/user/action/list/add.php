<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;

    set_exception_handler("webgloo_ajax_exception_handler");

    //login is required
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    }

    $items = Util::getArrayKey($_POST, "items");
    $itemIds = json_decode($items);
    $message = "" ;
    foreach($itemIds as $itemId) {
        $message = $message. "|".$itemId ;
    }

    $html = array("code" => 200 , "message" => $message);
    $html = json_encode($html);
    echo $html;
    exit ;
?>
