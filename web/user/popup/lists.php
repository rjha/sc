<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url ;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    set_exception_handler('webgloo_ajax_exception_handler');
    
    $fUrl = Url::current();

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
        $fwd = "/user/login.php?q=".$fUrl."&g_session_action=".$gSessionAction;
        header('location: '.$fwd);
    
    }

    $loginId = Login::getLoginIdInSession();
    $itemId = Util::getArrayKey($_POST, "itemId");

    $listDao = new \com\indigloo\sc\dao\Lists();
    $listRows = $listDao->getOnLoginId($loginId);
    $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$itemId,$fUrl);
    echo $html ;
?>
