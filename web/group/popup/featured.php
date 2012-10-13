<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\sc\util\Nest as Nest ;
    //public data
    //do not put user role check here

    set_exception_handler('webgloo_ajax_exception_handler');

    $groupDao = new \com\indigloo\sc\dao\Group();
    $collectionDao = new \com\indigloo\sc\dao\Collection();
    
    $row = $collectionDao->glget(Nest::fgroups());
    $feature_slug = empty($row) ? "" : $row["t_value"] ;

    $fgroups = $groupDao->slugToGroupsMap($feature_slug);
    $html = \com\indigloo\sc\html\Group::getCloud($fgroups); 
    echo $html;
?>
