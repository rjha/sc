<?php
    //sc/monitor/analytic/bookmarks.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\ui\Filter as Filter;

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
    $qparams = Url::getRequestQueryParams();
    $filters = array();

    $pageSize = 20 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $rows = $bookmarkDao->getTablePaged($paginator,$filters);
    //print_r($rows); exit ;

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Bookmarks </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        
    </head>

    <body>
        <style>
            .name { width:120px;}
        </style>
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
                        <h2>Bookmarks</h2>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    
                    <div class="mt20">
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            if (sizeof($rows) > 0) {
                                $startId = $rows[0]["id"];
                                $endId = $rows[sizeof($rows) - 1]["id"];
                            }

                            echo \com\indigloo\sc\html\Site::getBookmarkTable($rows);
                        ?>
                    </div>
                </div>
                 
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/analytic/bookmarks.php', $startId, $endId); ?>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



