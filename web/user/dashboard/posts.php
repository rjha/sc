<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\ui\Filter as Filter;

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $postDao = new \com\indigloo\sc\dao\Post();
    
    $qparams = Url::getRequestQueryParams();
    //filters
    $filters = array();
    //Always add login_id filter for user dashboard
    $model = new \com\indigloo\sc\model\Post();
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);

    $postDBRows = array();
    $pageSize = Config::getInstance()->get_value("user.page.items");    

    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $postDBRows = $postDao->getPaged($paginator,$filters);
    
    $baseURI = "/user/dashboard/posts.php" ;


?>


<!DOCTYPE html>
<html>

    <head>
        <title> items - <?php echo $loginName; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">

            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                 <div class="span12">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>

            </div>
            <?php FormMessage::render(); ?>

            <div class="row">
               
                <div class="span8 offset1">
                    <div id="page-message" class="hide-me"> </div>
                    <div id="widgets">
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            
                            if (sizeof($postDBRows) > 0) {
                                $startId = $postDBRows[0]['id'];
                                $endId = $postDBRows[sizeof($postDBRows) - 1]['id'];

                                foreach ($postDBRows as $postDBRow) {
                                    //output post widget html
                                    echo \com\indigloo\sc\html\Post::getWidget($postDBRow);
                                }

                            } else {
                                 
                                $message = "No items found" ;
                                $options = array("hkey" => "dashboard.item.create");
                                echo \com\indigloo\sc\html\Site::getNoResult($message,$options);
                            }

                        ?>
                    </div>

                </div>
                <div class="span3">
                    
                </div>
               
            </div>
        </div> <!-- container -->
        
        <?php 
            if(sizeof($postDBRows) >= $pageSize)
                $paginator->render($baseURI,$startId,$endId); 
            

        ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
         
        <script>
            
            $(document).ready(function(){
                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                });

                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                });

                
                webgloo.sc.toolbar.add();
                //fix twitter bootstrap alerts
                webgloo.sc.dashboard.fixAlert();
                webgloo.sc.Lists.init();

            });



        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



