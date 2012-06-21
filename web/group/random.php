<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $groups = $groupDao->getRandom(50);

    $title = "Random groups";

    $hasPagination = false ;
    $hasNavigation = true ;
    $hasAlpha = false ;

    include(APP_WEB_DIR.'/group/inc/body.inc');

?>
