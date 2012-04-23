<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $total = $groupDao->getTotalCount();

    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $pageSize =	100;
    $paginator = new Pagination($qparams,$total,$pageSize);	
    $groups = $groupDao->getPaged($paginator);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/all.php" ;
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

