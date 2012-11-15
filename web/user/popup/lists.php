<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    set_exception_handler('webgloo_ajax_exception_handler');
    
    //qurl is base64_encoded
    $qUrl = Util::tryArrayKey($_POST, "qUrl");
    $qUrl = empty($qUrl) ? base64_encode('/') : $qUrl ;

     
    if(!Login::hasSession()) {
        // no login case
        // add to favorites list - after login
        // create data object representing pending session action
        $actionObj = new \stdClass ;
        $actionObj->endPoint = "/qa/ajax/bookmark.php" ;

        $params = new \stdClass ;
        //substitute the loginId
        $params->loginId = "{loginId}" ;
        $params->itemId = Util::getArrayKey($_POST, "itemId");
        $params->action = UIConstants::SAVE_POST ;
        $actionObj->params = $params ;
        
        // action payload in URL
        $gSessionAction = base64_encode(json_encode($actionObj));
        $fwd = "/user/login.php?q=".$qUrl."&g_session_action=".$gSessionAction;
        header('location: '.$fwd);
    
    }

    $loginId = Login::getLoginIdInSession();
    $itemId = Util::getArrayKey($_POST, "itemId");

    $listDao = new \com\indigloo\sc\dao\Lists();
    $listRows = $listDao->getOnLoginId($loginId);
    $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$itemId,$qUrl);
    echo $html ;
?>
