<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    
    use \com\indigloo\sc\ui\Filter as Filter; 
    
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user record found for given login_id", E_USER_ERROR);
    }
    
    $postDao = new \com\indigloo\sc\dao\Post();
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

    //copy URL parameters
    $fparams = $qparams;
    //now unset ft param
    unset($fparams["ft"]);
    //create filter Urls
    $ftBaseUrl = Url::createUrl("/user/dashboard/posts.php",$fparams);
    $ftFeaturedUrl = Url::addQueryParameters($ftBaseUrl, array("ft" => "featured"));
    
    //filters
    $filters = array();
    $model = new \com\indigloo\sc\model\Post();
    $ft = Url::tryQueryParam("ft");
        
    if(!is_null($ft)) {
       
        switch($ft){
            case 'featured' :
                $filter = new Filter($model);
                $filter->add($model::FEATURED,Filter::EQ,TRUE);
                array_push($filters,$filter);
                break;
            default:
                break;
                
        }
    }
    
    //Always add login_id filter for user dashboard
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);
     
    $postDBRows = array();
    $total = $postDao->getTotalCount($filters);

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $postDBRows = $postDao->getPaged($paginator, $filters);
    
?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
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
                <?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
                </div> 

            </div>

            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    <div class="page-header"> 
                        <h2> Posts </h2> 
                        <div class="btn-group">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                Filter Posts
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $ftBaseUrl; ?>">All Posts</a></li>
                                <li><a href="<?php echo $ftFeaturedUrl; ?>">Featured Posts</a></li>
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
                                echo \com\indigloo\sc\html\Post::getWidget($postDBRow);
                            }
                        ?>
                   
                </div>
                <div class="span3">
                     <?php include('inc/menu.inc'); ?>
                </div>
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/user/dashboard/posts.php', $startId, $endId); ?>

        <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



