<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

    use \com\indigloo\sc\auth\Login as Login ;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $loginId = Login::getLoginIdInSession();
    $cloudGroups = $groupDao->getOnLoginId($loginId);

    $view = $_SERVER['APP_WEB_DIR']. '/view/cloud.php' ;
    include($view); 
?>
