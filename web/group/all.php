<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\ui\Filter as Filter;


    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $filters = array();

    $groupDao = new \com\indigloo\sc\dao\Group();
    $total = $groupDao->getTotalCount($filters);

    $pageSize = 100;
    $paginator = new Pagination($qparams,$total,$pageSize); 
    $groups = $groupDao->getPaged($paginator,$filters);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/all.php" ;
    $title = "All groups";

    $hasPagination = true ;
    $hasNavigation = true ;
    $hasAlpha = false ;

    include(APP_WEB_DIR.'/group/inc/body.inc');
?>
