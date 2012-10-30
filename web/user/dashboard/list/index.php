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
    

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $listDao = new \com\indigloo\sc\dao\Lists();
    
    $qparams = Url::getRequestQueryParams();
    $total = $listDao->getTotalOnLoginId($loginId);

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $listDBRows = $listDao->getPagedOnLoginId($paginator,$loginId);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $gSessionLogin->name ; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
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
                <div class="span8 offset1 mh600">
                    
                    <?php FormMessage::render(); ?>
                    <div id="widgets">
                    <?php 
                        $startId = NULL;
                        $endId = NULL;

                        if (sizeof($listDBRows) > 0) {
                            $startId = $listDBRows[0]["id"];
                            $endId = $listDBRows[sizeof($listDBRows) - 1]["id"];

                            foreach($listDBRows as $listDBRow) {
                                echo \com\indigloo\sc\html\Lists::getWidget($listDBRow);
                            }

                        }
                     ?>
                     </div> <!-- widgets -->
                        
                </div>
               
            </div>
        </div> <!-- container -->
        
        <?php $paginator->render('/user/dashboard/posts.php', $startId, $endId); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script src="/js/sc.js" type="text/javascript"> </script>

        <script>
            
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
                //turn off border for last widget
                $("#widgets .widget:last-child").css('border-bottom', 'none');
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



