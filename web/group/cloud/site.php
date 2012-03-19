<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    $groupDao = new \com\indigloo\sc\dao\Group();

    $cloudGroups = array();
    $i = 1 ;
    while($i <= 3) {
        $rows = $groupDao->getLatest(100);
        $cloudGroups = array_merge($cloudGroups,$rows);
        if(sizeof($rows) == 0 ) break;
        $i++;
    }

    $view = $_SERVER['APP_WEB_DIR']. '/view/cloud.php' ;
    include($view); 
?>
