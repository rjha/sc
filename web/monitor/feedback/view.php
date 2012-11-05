<?php

    //sc/monitor/feedback.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

   
    $qparams = Url::getRequestQueryParams();
    $feedbackDao = new \com\indigloo\sc\dao\Feedback();
    
    $pageSize = 20;
    $paginator = new \com\indigloo\ui\Pagination($qparamss,$pageSize);
    $feedbackDBRows = $feedbackDao->getPaged($paginator);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - feedback by users  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        <script>
            $(document).ready(function(){
                //show options on widget hover
                
                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                    /* @todo move colors to a css style */
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
                        <h2>Feedback</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    <div id="widgets">
                        <?php
                            $startId = NULL ;
                            $endId = NULL ;

                            if(sizeof($feedbackDBRows) > 0 ) {
                                $startId = $feedbackDBRows[0]['id'];
                                $endId = $feedbackDBRows[sizeof($feedbackDBRows)-1]['id'];
                            }

                            foreach($feedbackDBRows as $feedbackDBRow) {
                                echo \com\indigloo\sc\html\Feedback::get($feedbackDBRow);
                            }
                        ?>
                    </div>

                </div>
                
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/feedback/view.php', $startId, $endId); ?>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


