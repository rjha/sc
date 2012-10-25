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
    $listDao = new \com\indigloo\sc\dao\Lists();
    $loginId = Login::getLoginIdInSession();
    $listRows = $listDao->get($loginId);

    $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$strItems,$qUrl);
    echo $html ;

?>
