<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    
    
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $listDao = new \com\indigloo\sc\dao\Lists();
    
    $qparams = Url::getRequestQueryParams();
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $listDBRows = $listDao->getPagedOnLoginId($paginator,$loginId);
    $baseURI = "/user/dashboard/list/index.php" ;

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
            
            <div class="row">
                 <div class="span12">
                   <div class="page-header">&nbsp;</div>
                </div>

            </div>
            
            <div class="row">
                <div class="span8 offset1">
                    
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

                        } else {
                            $message = "No Lists found " ;
                            echo \com\indigloo\sc\html\Site::getNoResult($message);
                        }
                     ?>
                     </div> <!-- widgets -->
                        
                </div>
               
            </div>
        </div> <!-- container -->
        <?php 
            if(sizeof($listDBRows) >= $pageSize)
                $paginator->render($baseURI,$startId,$endId); 
        ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
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



