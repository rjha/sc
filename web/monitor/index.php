<?php
    //sc/monitor/index.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\sc\ui\Constants as UIConstants;
    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\ui\Filter as Filter;

    //url decoded parameters
   
    $qparams = Url::getRequestQueryParams();
    $options = UIConstants::WIDGET_ALL ;


    //copy URL parameters
    $fparams = $qparams;
    // unset extra ft params and search token param
    unset($fparams["ft"]);
    unset($fparams["gt"]);
    //ft urls start with page 1
    $fparams["gpage"] = 1 ;
    //create filter Urls
    $ftBaseUrl = Url::createUrl("/monitor/index.php",$fparams);
    $ftFeaturedUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "featured"));
    $ft24hoursUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "24hours"));

    //search clear link
    $sparams = $qparams ;
    unset($sparams["gt"]);
    $clearSearchUrl = Url::createUrl("/monitor/index.php",$sparams);


    //post filters
    $filters = array();
    $model = new \com\indigloo\sc\model\Post();
    $ft = Url::tryQueryParam("ft");
    $ftname = "";
    $gtoken = Util::tryArrayKey($qparams,"gt");
    $itemId = NULL ;

    if( (strlen($gtoken) > 5) && (strcmp(substr($gtoken,0,5), "item:") == 0)){
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

    if(!empty($gtoken)) {
        //@todo - add item:number token as well.
        //get matching ids from sphinx
        $sphinx = new \com\indigloo\sc\search\SphinxQL();
        $total = $sphinx->getPostsCount($gtoken);
        $paginator = new Pagination($qparams,$total,$pageSize);
        $ids = $sphinx->getPagedPosts($gtoken,$paginator);
        $sphinx->close();

        if(sizeof($ids) > 0 ) {
            $postDBRows = $postDao->getOnSearchIds($ids) ;
        }

    } else {

        $total = $postDao->getTotalCount($filters);
        $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
        $postDBRows = $postDao->getPaged($paginator,$filters);
        $gtoken = "" ;
    }


?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - All posts  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
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
                                <input id="site-search" name="gt" type="text" class="search-query" placeholder="Quick Search">
                                <input type="hidden" name="ft" value="<?php echo $ft; ?>"/>
                            </form>

                        </div>

                        <div class="span5">
                            <span class="label label-warning"> Filter </span>
                            &nbsp;
                            <a href="<?php echo $ftFeaturedUrl; ?>">Featured</a>
                            &nbsp;|&nbsp;
                            <a href="<?php echo $ft24hoursUrl; ?>">Last 24 Hours</a>
                            &nbsp;|&nbsp;
                            <a href="/monitor/index.php">All Posts</a>

                        </div>

                    </div> <!-- row -->

                    <div class="p20">
                        <span class="color-red">
                            Applied filters = <?php echo $gtoken; ?>  <?php echo $ftname; ?>
                        </span>
                        <span> | <?php echo $total; ?> results </span>
                        <span> ( hint: item:itemno and + operator works in search box) </span>
                    </div>

                    <?php
                        $startId = NULL;
                        $endId = NULL;
                        if (sizeof($postDBRows) > 0) {
                            $startId = $postDBRows[0]["id"];
                            $endId = $postDBRows[sizeof($postDBRows) - 1]["id"];
                        }

                        foreach ($postDBRows as $postDBRow) {
                            echo \com\indigloo\sc\html\Post::getAdminWidget($postDBRow,$options);
                        }
                    ?>

                </div>
                 
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/index.php', $startId, $endId); ?>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



