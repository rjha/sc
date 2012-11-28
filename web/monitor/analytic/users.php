<?php
    //sc/monitor/analytic/users.php
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


    $sortVariable ="followers";

    $pageSize = 25 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $baseURI = "/monitor/analytic/users.php";
    
    $userDao = new \com\indigloo\sc\dao\User();
    
    switch($tab) {
        case 1 :
            $sortVariable ="followers";
            break ;
        case 2:
            $sortVariable ="likes";
            break ;
        case 3:
            $sortVariable ="comments";
            break ;
        case 4:
            $sortVariable ="posts";
            break ;
       case 5:
            $sortVariable ="followings";
            break ;

        default :
            $sortVariable ="followers";
    }

    $zsetKey = Nest::score("user", $sortVariable);
    $members = $redis->getPagedZSet($zsetKey,$paginator);

    //first one is id, second one is score
    $count = 0 ;
    $scores = array();
    $ids = array();

    for($i = 1 ; $i < sizeof($members); $i++) {
        if($i % 2 == 0) {
            array_push($scores,$members[$i-1]);
        }else {
            $loginId = $members[$i-1];
            array_push($ids,$loginId);
        }

    }
    
    $rows = $userDao->getOnSearchLoginIds($ids);
     
    $pageNo = $paginator->getPageNo();
    $startId = ($pageNo-1)*$pageSize ;
    $endId = ($startId + $pageSize) -1 ;

    $rowsHtml = "" ;
    $gNumRecords = sizeof($rows) ;

    foreach ($rows as $row) {
        $rowsHtml .= \com\indigloo\sc\html\User::getAdminWidget($row);
    }
                        

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user analytic </title>
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
                        <h2> Active users</h2>
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
                            <a href="<?php echo $baseURI ;?>?tab=1">followers</a>
                        </span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=2">likes</a>
                        </span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=3">comments</a>
                        </span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=4">posts</a>
                        </span>
                        <span class="p10">
                            <a href="<?php echo $baseURI ;?>?tab=5">followings</a>
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



