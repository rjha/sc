<?php
    //sc/monitor/comments.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\sc\ui\Constants as UIConstants;

    $qparams = Url::getRequestQueryParams();

    $commentDao = new \com\indigloo\sc\dao\Comment() ;
    
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $commentDBRows = $commentDao->getPaged($paginator);
    $baseURI = "/monitor/comments.php" ;

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - All Comments  </title>
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
                        <h2>Comments</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                 <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">

                        <?php
                            $startId = NULL ;
                            $endId = NULL ;

                            if(sizeof($commentDBRows) > 0 ) {
                                $startId = $commentDBRows[0]['id'] ;
                                $endId =   $commentDBRows[sizeof($commentDBRows)-1]['id'] ;
                            }

                            foreach($commentDBRows as $commentDBRow){
                                echo \com\indigloo\sc\html\Comment::getWidget($commentDBRow,UIConstants::COMMENT_USER);
                            }
                        ?>

                </div>
                 
            </div>
        </div> <!-- container -->
        
         <?php if(sizeof($commentDBRows) >= $pageSize) 
            $paginator->render($baseURI,$startId,$endId);  ?>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



