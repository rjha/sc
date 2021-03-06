<?php
    //sc/ajax/url/extract.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    set_exception_handler('webgloo_ajax_exception_handler');

    use \com\indigloo\text\UrlParser as UrlParser;

    //Get URL from ajax request 
    $url = $_POST['q'];
    $parser = new UrlParser();

    $data = $parser->extract($url);
    $payload = array('code' => 1 , 'data' => $data , 'message' => 'success');
    echo json_encode($payload);
    
?>
