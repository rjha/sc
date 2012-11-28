<?php
    //sc/monitor/analytic/posts.php
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
    $tab = (isset($qparams["tab"])) ? $qparams["tab"] : 1;
    settype($tab,"integer");


    $sortVariable ="likes";

    $pageSize = 25 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $baseURI = "/monitor/analytic/posts.php";
    
    $postDao = new \com\indigloo\sc\dao\Post();
     
    switch($tab) {
        case 1 :
            $sortVariable ="likes";
            break ;
        case 2:
            $sortVariable ="comments";
            break ;
        default :
            $sortVariable ="likes";
    }

    $zsetKey = Nest::score("post", $sortVariable);
    $members = $redis->getPagedZSet($zsetKey,$paginator);

    $count = 0 ;
    $scores = array();
    $ids = array();

    if(sizeof($members) >= 2 ){
        for($i = 1 ; $i < sizeof($members); $i++) {
            // odd ones are members
            // evens are scores

            if($i % 2 != 0) {
                $itemId = $members[$i-1];
                $postId = PseudoId::decode($itemId);
                array_push($ids,$postId);

                //score is next one
                $scores[$itemId] = (isset($members[$i])) ? $members[$i] : 0 ;
            }

        }
    }

    $rows = $postDao->getOnSearchIds($ids);
     
    $pageNo = $paginator->getPageNo();
    $startId = ($pageNo-1)*$pageSize ;
    $endId = ($startId + $pageSize) -1 ;

    $rowsHtml = "" ;
    $gNumRecords = sizeof($rows) ;

    foreach ($rows as $row) {
        $score =  isset($scores[$row["pseudo_id"]]) ? $scores[$row["pseudo_id"]] : 0 ;
        $rowsHtml .= \com\indigloo\sc\html\Post::getAdminWidget($row,$score);

    }
                        

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - post analytic </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>
        <style>
            /* @inpage @hardcoded */
            #action-links {
                padding-bottom:40px;
            }

            #action-links a {
                
                border-bottom: 3px solid #FE63BB;
                padding: 0 4px;
                margin: 1px 12px 0;
                font-weight: bold;
            }

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
                        <h2> Popular posts</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    <div id="action-links">
                        <span class="b">sorted on <?php echo $sortVariable; ?> / sort on</span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=1"> likes</a>
                        </span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=2">comments</a>
                        </span>

                    </div>

                    <?php echo $rowsHtml ?>
                    
                </div>
                 
            </div>
        </div> <!-- container -->
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script>
            $(document).ready(function(){
                //show options on widget hover
                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                    $(this).css("background-color", "#FEFDF1");
                });
                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                    $(this).css("background-color", "#FFFFFF");
                }); 

                webgloo.sc.item.addAdminActions();


            });

        </script>

        <?php $paginator->render($baseURI,$startId,$endId,$gNumRecords);  ?>
        
        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



