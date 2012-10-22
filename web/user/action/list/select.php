<?php
    
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

    $strItems = Util::getArrayKey($_POST, "items");
    $qUrl = Util::getArrayKey($_POST, "qUrl");

    // lists
    $list = array(
        0 => "reshma ki jawani", 
        1 => "aaya toofan bhaga shaitan",
        2 => "This is going to be a very (unreasonably long list name - that normal people do not see",
        3 => "My Favorites",
        4 => "Gabbar ki Mohabbat",
        5 => "shaitaan ki suhaagrat",
        6 => "Do jism ek Jaan",
        7 => "Andheri raat ka saathi",
        8 => "Pyaas bhujti nahin hai");

    $html = \com\indigloo\sc\html\Site::renderList($list,$strItems,$qUrl);
    echo $html ;

?>