<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    //public data
    //do not put user role check here

    set_exception_handler('webgloo_ajax_exception_handler');

    $groupDao = new \com\indigloo\sc\dao\Group();
    $limit = 61 ;
    $navGroups = $groupDao->getLatest($limit);
    
    $html = \com\indigloo\sc\html\Group::getCloud($navGroups); 
    echo $html;
?>