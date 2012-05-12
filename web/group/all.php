<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\ui\Filter as Filter;


    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $filters = array();

    /*
    $ft = Utill::tryArrayKey($qparams,"ft");


    if(!is_null($ft)) {
        switch($ft) {
            case 'user' :
                $model = new \com\indigloo\sc\model\Group();
                $loginId = Login::getLoginIdInSession();
                $filter = new Filter($model);
                $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
                array_push($filters,$filter);

            default:
                trigger_error("Unknown group filter",E_USER_ERROR);
                break;
        }


    } */

    $groupDao = new \com\indigloo\sc\dao\Group();
    $total = $groupDao->getTotalCount($filters);

    $pageSize =	100;
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
    $hasNavigation = true ;

    include($_SERVER['APP_WEB_DIR'].'/group/inc/body.inc');

?>
   </div> <!-- container -->
   <hr>
    <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>
    <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
    </div>

    </body>
</html>

