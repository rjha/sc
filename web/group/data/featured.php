<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    //public data
    //do not put user role check here

    set_error_handler('webgloo_ajax_error_handler');

    $groupDao = new \com\indigloo\sc\dao\Group();
    $feature_slug = $groupDao->getFeatureSlug();
    $fgroups = $groupDao->slugToGroups($feature_slug);
    $limit = 0 ;
    $lgroups = array();

    if(sizeof($fgroups) < 50 ) {
       $limit = (50 - sizeof($fgroups)) + 10 ;
       $lgroups = $groupDao->getLatest($limit);
    }

    $navGroups = array_merge($fgroups,$lgroups);
    //$navGroups = array_slice($navGroups,0,30);
    $html = \com\indigloo\sc\html\Group::getCloud($navGroups); 
    echo $html;
?>
