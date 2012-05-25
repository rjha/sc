<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    $groupDao = new \com\indigloo\sc\dao\Group();
    $cloudGroups = $groupDao->getLatest(100);
    $view = APP_WEB_DIR. '/view/cloud.php' ;
    include($view); 
?>
