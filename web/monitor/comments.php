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

    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

    $commentDao = new \com\indigloo\sc\dao\Comment() ;
    $total = $commentDao->getTotalCount();

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
    $commentDBRows = $commentDao->getPaged($paginator);
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
                $('.widget .options').hide();
                $('.widget').mouseenter(function() {
                    $(this).find('.options').toggle();
                    $(this).css("background-color", "#FEFDF1");
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
                <?php include(APP_WEB_DIR . '/monitor/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
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
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->

        <?php $paginator->render('/monitor/comments.php', $startId, $endId); ?>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



