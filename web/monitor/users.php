<?php

    //sc/monitor/users.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\ui\Filter as Filter;


    // paginate

    $qparams = Url::getRequestQueryParams();
    $userDao = new \com\indigloo\sc\dao\User();

    //filters
    $filters = array();
    $total = $userDao->getTotal($filters);
    $pageSize = 20 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $userDBRows = $userDao->getPaged($paginator,$filters);



    //past 24 hour filter
    $filters = array();
    $model = new \com\indigloo\sc\model\User();
    $filter = new Filter($model);
    $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
    array_push($filters,$filter);
    $l24hTotal = $userDao->getTotal($filters);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - users in system</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/monitor/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR.'/monitor/inc/top-unit.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>Users</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span10">


                    <h3> Total <?php echo $total; ?> / Last 24 HR <?php echo $l24hTotal; ?>   </h3>

                     <?php
                            $startId = NULL;
                            $endId = NULL;
                            if (sizeof($userDBRows) > 0) {
                                $startId = $userDBRows[0]['id'];
                                $endId = $userDBRows[sizeof($userDBRows) - 1]['id'];
                            }

                            echo \com\indigloo\sc\html\User::getTable($userDBRows);
                        ?>

                </div>
                 
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/users.php', $startId, $endId); ?>


        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


