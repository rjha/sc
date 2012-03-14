<?php

    $questionDao = new \com\indigloo\sc\dao\Question();
    $filter = array($questionDao::FEATURE_COLUMN => 1);
    $questionDBRows = $questionDao->getPosts($filter,50);

    $template = $_SERVER['APP_WEB_DIR']. '/view/inc/tiles.php';
    include($template); 

?>
