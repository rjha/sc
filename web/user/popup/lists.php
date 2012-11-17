<?php 
    
    // user/popup/list.php
    // normal HTML output
    // session pending action not possible.

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    set_exception_handler('webgloo_ajax_exception_handler');
    
    // list popup is called via javascript on pages
    // so actual "form caller " is what is coming in as
    // qUrl (original window.location.href) from javascript POST 
    // this is base64 encoded
    $qUrl = Util::getArrayKey($_POST, "qUrl");

    if(!Login::hasSession()) {
        $message = "You need to login!";
        echo $message ;
        exit ;
    }

    $loginId = Login::getLoginIdInSession();
    $itemId = Util::getArrayKey($_POST, "itemId");

    $listDao = new \com\indigloo\sc\dao\Lists();
    $listRows = $listDao->getOnLoginId($loginId);

    // Add default rows to top of lists
    // 

    $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$itemId,$qUrl);
    echo $html ;
?>
