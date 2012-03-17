<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    $groupDao = new \com\indigloo\sc\dao\Group();
    //@todo - get random groups here ..
    $cloudGroups = $groupDao->getLatest(100);

    $view = $_SERVER['APP_WEB_DIR']. '/view/cloud.php' ;
    include($view); 
?>
