<?php
    //sc/monitor/analytic/bookmarks.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\sc\redis as redis ;

    use \com\indigloo\sc\util\Nest ;
    use \com\indigloo\sc\util\PseudoId ;

    $qparams = Url::getRequestQueryParams();
    $redis = new redis\Activity();


    $pageSize = 25 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $baseURI = "/monitor/analytic/popular.php";
    
    $zsetKey = Nest::score("post", "likes");
    $members = $redis->getPagedZSet($zsetKey,$paginator);
    //first one is id, second one is score
    $count = 0 ;
    $scores = array();
    $ids = array();

    for($i = 1 ; $i < sizeof($members); $i++) {
        if($i % 2 == 0) {
            array_push($scores,$members[$i-1]);
        }else {
            $itemId = $members[$i-1];
            $postId = PseudoId::decode($itemId);
            array_push($ids,$postId);
        }

    }

    //get post rows using ids
    $postDao = new \com\indigloo\sc\dao\Post();
    $rows = $postDao->getOnSearchIds($ids);
     
    $startId = NULL;
    $endId = NULL;
    $rowsHtml = "" ;

    $gNumRecords = sizeof($rows) ;

    if ( $gNumRecords > 0) {
        $startId = $rows[0]["id"];
        $endId = $rows[$gNumRecords - 1]["id"];
    }

    foreach ($rows as $row) {
        $rowsHtml .= \com\indigloo\sc\html\Post::getAdminWidget($row);
    }
                        

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - what is popular </title>
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
                        <h2>what is popular?</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    
                 
                    <?php echo $rowsHtml ?>
                    
                </div>
                 
            </div>
        </div> <!-- container -->
        
        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



