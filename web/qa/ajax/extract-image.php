<?php
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Logger as Logger ;

    set_exception_handler('webgloo_ajax_exception_handler');
    $message = NULL ;

    //use login is required for bookmarking
    /*
    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $html = json_encode($message);
        echo $html;
        exit;
    } */

    $target = $_POST["target"] ;
    $parser = new \com\indigloo\text\UrlParser();
    $response = $parser->extractUsingDom($target);

    if(empty($response)) {
        $response = new \stdClass ;
        $response->code = 500 ;
        $response->message = "Error retrieving images. Please try again.";
    } else {
        $response->code = 200 ;
        $count = count($response->images);
        if($count == 0 )
            $response->message = "success: No image found on target URL";
        else
            $response->message = sprintf("success : retrieved %d images.",$count);

    }

    $html = json_encode($response);
    echo $html;
?>
