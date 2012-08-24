<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\ui\Filter as Filter;

    $ft  = Url::tryQueryParam("ft");
    if(empty($ft)) {
        $ft = 'a' ;
    }

    $groupDao = new \com\indigloo\sc\dao\Group();

    // Alpha filter
    $filters = array();
    $model = new \com\indigloo\sc\model\Group();
    $filter = new Filter($model);
    $filter->add($model::TOKEN,Filter::LIKE,$ft);
    array_push($filters,$filter);

    $total = $groupDao->getTotalCount($filters);
    
    $qparams = Url::getRequestQueryParams();
    $pageSize = 50;
    $paginator = new Pagination($qparams,$total,$pageSize); 
    $groups = $groupDao->getPaged($paginator,$filters);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/alpha.php" ;
    $title = "All groups";

    $hasPagination = true ;
    $hasNavigation = true ;
    $hasAlpha = true ;

    include(APP_WEB_DIR.'/group/inc/body.inc');

?>
