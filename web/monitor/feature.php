
<div class="row">
    <div class="span9">


    </div>



</div>



<?php

    $postDao = new \com\indigloo\sc\dao\Post();
    $filter = array($postDao::FEATURE_COLUMN => 1);
    $postDBRows = $postDao->getPosts($filter,50);

    $template = $_SERVER['APP_WEB_DIR']. '/view/inc/tiles.php';
    include($template); 

?>

