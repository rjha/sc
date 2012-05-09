<?php
    //sc/monitor/index.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\sc\ui\Constants as UIConstants;
    use \com\indigloo\sc\ui\Filter as Filter; 
    
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $options = UIConstants::WIDGET_ALL ;
    
    $postDao = new \com\indigloo\sc\dao\Post();

    //filters
    $featuredUrl = Url::addQueryParameters($_SERVER['REQUEST_URI'], array("ft" => "featured"));
    
    $ft = Url::tryQueryParam("ft");
    $filters = array();
    $target = new \com\indigloo\sc\model\Post();
        
    if(!is_null($ft)) {
       
        switch($ft){
            case 'featured' :
                $filter = new Filter($target);
                $filter->add($target::FEATURED,TRUE);
                array_push($filters,$filter);
                break;
            default:
                break;
                
        }
    }
    
    //Always add this filter
    $filter = new Filter($target);
    $filter->add($target::LOGIN_ID, $loginId);
    array_push($filters,$filter);
     
    $postDBRows = array();
    $total = $postDao->getTotalCount($filters);
    
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $postDBRows = $postDao->getPaged($paginator,$filters);


?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - All posts  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        
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
            });
            
        </script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR'] . '/monitor/inc/toolbar.inc'); ?>
                </div> 

            </div>

            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR']. '/monitor/inc/banner.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    <div class="page-header"> 

                        <h2><?php echo $total ?> Posts </h2> 
                        <div class="btn-group">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                Filter*
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">All Posts</a></li>
                                <li><a href="<?php echo $featuredUrl; ?>">Featured Posts</a></li>
                            </ul>
                        </div> <!-- button group -->

                    </div>
                    
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            if (sizeof($postDBRows) > 0) {
                                $startId = $postDBRows[0]['id'];
                                $endId = $postDBRows[sizeof($postDBRows) - 1]['id'];
                            }

                            foreach ($postDBRows as $postDBRow) {
                                echo \com\indigloo\sc\html\Post::getWidget($postDBRow,$options);
                            }
                        ?>
                   
                </div>
                <div class="span3">
                     <?php include($_SERVER['APP_WEB_DIR'].'/monitor/inc/menu.inc'); ?>
                </div>
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/index.php', $startId, $endId); ?>

        <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



