<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\ui\Filter as Filter;

    
    $qparams = Url::getRequestQueryParams();
    $login = Login::getLoginInSession();
    $loginId  = $login->id;

    $groupDao = new \com\indigloo\sc\dao\Group();

    $filters = array();
    $model = new \com\indigloo\sc\model\Group();
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);


    $total = $groupDao->getCountOnLoginId($loginId);
    $pageSize = 100;
    $paginator = new Pagination($qparams,$total,$pageSize); 
    $groups = $groupDao->getPagedUserGroups($paginator,$filters);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/user/all.php" ;
    $title = sprintf("%s's groups",$login->name);
    $hasNavigation = false ;
    include(APP_WEB_DIR."/view/group/folder.php");


?>
  
