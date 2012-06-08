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
    use \com\indigloo\ui\Filter as Filter; 
    
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $options = UIConstants::WIDGET_ALL ;
    
    $postDao = new \com\indigloo\sc\dao\Post();

    //copy URL parameters
    $fparams = $qparams;
    //now unset extra params
    unset($fparams["ft"]);
    //ft urls start with page 1
    $fparams['gpage'] = 1 ;
    //create filter Urls
    $ftBaseUrl = Url::createUrl("/monitor/index.php",$fparams);
    $ftFeaturedUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "featured"));
    $ft24hoursUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "24hours"));
    $ft3daysUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "3days"));

    //search clear link
    $sparams = $qparams ;
    unset($sparams["gt"]);
    $clearSearchUrl = Url::createUrl("/monitor/index.php",$sparams);

    
    //filters
    $filters = array();
    $model = new \com\indigloo\sc\model\Post();
    $ft = Url::tryQueryParam("ft");
    $ftname = '';
        
    if(!is_null($ft)) {
       
        switch($ft){
            case 'featured' :
                $filter = new Filter($model);
                $filter->add($model::FEATURED,Filter::EQ,TRUE);
                array_push($filters,$filter);
                $ftname = 'Featured';
                break;
            case '24hours' :
                $filter = new Filter($model);
                $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
                array_push($filters,$filter);
                $ftname = 'Last 24 hour';
                break;
            case '3days' :
                $filter = new Filter($model);
                $filter->add($model::CREATED_ON,Filter::GT,"3 DAY");
                array_push($filters,$filter);
                $ftname = 'Last 3 Days';
                break;
            default:
                break;
        }
    }

    $postDBRows = array();
    $total = $postDao->getTotalCount($filters);
    
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $postDBRows = $postDao->getPaged($paginator,$filters);

    $gtoken = Util::tryArrayKey($qparams,"gt");
    //webgloo Url qparams are urlencoded 
    //we need to decode before passing to DB layer
    $gtoken = urldecode($gtoken);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - All posts  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/js/sc.js"></script>
        
        <script>
            $(document).ready(function(){
                //show options on widget hover
                $('.widget .options').hide();
                $('.widget').mouseenter(function() { 
                    $(this).find('.options').toggle(); 
                    $(this).css("background-color", "#F0FFFF");
                });
                $('.widget').mouseleave(function() { 
                    $(this).find('.options').toggle(); 
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
                <?php include(APP_WEB_DIR. '/monitor/inc/banner.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                <?php include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                        <div class="row">
                           <div class="span5">
                            <!-- <h3> <?php echo $ftname; ?> -  <?php echo $total ?> Posts </h3>  -->
                            <form method="GET" action="<?php echo $clearSearchUrl; ?>">
                            <input id="site-search" name="gt" type="text" class="search-query" placeholder="Quick Search"> &nbsp;<a href="<?php echo $clearSearchUrl; ?>">clear</a>
                            <input type="hidden" name="ft" value="<?php echo $ft; ?>"/>
                            </form>
                            
                           </div>
                            <div class="span4">
                                <div class="btn-group">
                                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                        Filter&nbsp;Results
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $ftBaseUrl; ?>">All Posts</a></li>
                                        <li><a href="<?php echo $ftFeaturedUrl; ?>">Featured Posts</a></li>
                                        <li><a href="<?php echo $ft24hoursUrl; ?>">Last 24 Hours</a></li>
                                        <li><a href="<?php echo $ft3daysUrl; ?>">Last 3 days</a></li>
                                    </ul>
                                </div> <!-- button group -->
                               </div>
                        </div> <!-- row -->
                        <div class="p10">
                            <span class="b"> filter </span> 
                            <span class="color-red"><?php echo $gtoken; ?> / <?php echo $ftname; ?> </span>
                            <span> <?php echo $total; ?> results
                        </div>

                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            if (sizeof($postDBRows) > 0) {
                                $startId = $postDBRows[0]['id'];
                                $endId = $postDBRows[sizeof($postDBRows) - 1]['id'];
                            }

                            foreach ($postDBRows as $postDBRow) {
                                echo \com\indigloo\sc\html\Post::getAdminWidget($postDBRow,$options);
                            }
                        ?>
                   
                </div>
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/index.php', $startId, $endId); ?>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



