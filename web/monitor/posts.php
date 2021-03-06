<?php
    //sc/monitor/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\sc\ui\Constants as UIConstants;
    use \com\indigloo\sc\Constants as AppConstants;

    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\ui\Filter as Filter;

    //url decoded parameters
   
    $qparams = Url::getRequestQueryParams();
    
    //copy URL parameters
    $fparams = $qparams;
    // unset extra ft params and search token param
    unset($fparams["ft"]);
    unset($fparams["gt"]);
    //ft urls start with page 1
    $fparams["gpage"] = 1 ;
    //create filter Urls
    $ftBaseUrl = Url::createUrl("/monitor/posts.php",$fparams);
    $ftFeaturedUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "featured"));
    $ft24hoursUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "24hours"));

    //search clear link
    $sparams = $qparams ;
    unset($sparams["gt"]);
    $clearSearchUrl = Url::createUrl("/monitor/posts.php",$sparams);


    //post filters
    $filters = array();
    $model = new \com\indigloo\sc\model\Post();
    $ft = Url::tryQueryParam("ft");
    $ftname = "";
    $gtoken = Util::tryArrayKey($qparams,"gt");
    $itemId = NULL ;

    if(empty($ft) 
        && (strlen($gtoken) > 5) 
        && (strcmp(substr($gtoken,0,5), "item:") == 0)){
        $ft = "item" ;
        $itemId = substr($gtoken,5);
        //reset search token
        $gtoken = NULL ;
    }

    if(!is_null($ft)) {
        switch($ft){
            case "featured" :
                $filter = new Filter($model);
                $filter->add($model::FEATURED,Filter::EQ,TRUE);
                array_push($filters,$filter);
                $ftname = "Featured";
                break ;

            case "24hours" :
                $filter = new Filter($model);
                $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
                array_push($filters,$filter);
                $ftname = "Last 24 hour";
                break;
            case "item" :
                $filter = new Filter($model);
                $filter->add($model::ITEM_ID,Filter::EQ,$itemId);
                array_push($filters,$filter);
                $ftname = "Item:".$itemId ;
                break;
            default:
                break;
        }
    }


    $postDBRows = array();
    $postDao = new \com\indigloo\sc\dao\Post();
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $total = 0 ;

    if(empty($ft) && !empty($gtoken)) {
        
        $sphinx = new \com\indigloo\sc\search\SphinxQL();
        $total = $sphinx->getPostsCount($gtoken);
        $paginator = new Pagination($qparams,$pageSize);
        $ids = $sphinx->getPagedPosts($gtoken,$paginator);
        $sphinx->close();

        if(sizeof($ids) > 0 ) {
            $postDBRows = $postDao->getOnSearchIds($ids) ;
        }

        $ftname = $gtoken ;

    } else {

        $total = $postDao->getTotalCount($filters);
        $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
        $postDBRows = $postDao->getPaged($paginator,$filters);
    }

    $baseURI ="/monitor/posts.php" ;

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - All posts  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
       

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
                        <h2>Posts</h2>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    <div class="row">
                        <div class="span4">
                            <form method="GET" action="<?php echo $clearSearchUrl; ?>">
                                <input id="site-search" name="gt" type="text" class="search-query" placeholder="Search...">
                                <input type="hidden" name="ft" value="<?php echo $ft; ?>"/>
                            </form>

                        </div>

                        <div class="span4">
                            <div class="faded-text">
                                <ul class="unstyled">
                                    <li> type item:&lt;item_no&gt;, e.g. item:8566 for a single item </li>
                                    <li>  use + operator, e.g. saree+work to narrow down results </li>
                                </ul> 
                            </div>
                           
                        </div>

                    </div> <!-- row -->

                    <div class="p10">
                        <span class="b"> Total: <?php echo $total; ?> </span>
                        <span class="color-red">
                            &nbsp;filters (<?php echo $ftname; ?>)
                        </span>
                        &nbsp;
                        <a href="/monitor/posts.php">All Posts</a>
                        &nbsp;|&nbsp;
                        <a href="<?php echo $ftFeaturedUrl; ?>">Featured</a>
                        &nbsp;|&nbsp;
                        <a href="<?php echo $ft24hoursUrl; ?>">Last 24HR</a>
                        
                    </div>
                    <div class="mt20">
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            $gNumRecords = sizeof($postDBRows) ;

                            if ( $gNumRecords > 0) {
                                $startId = $postDBRows[0]["id"];
                                $endId = $postDBRows[$gNumRecords - 1]["id"];
                            }

                            foreach ($postDBRows as $postDBRow) {
                                echo \com\indigloo\sc\html\Post::getAdminWidget($postDBRow);
                            }
                        ?>
                    </div>
                </div>
                 
            </div>
        </div> <!-- container -->
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script language="javascript" type="text/javascript" src="/js/monitor.js"></script>

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



