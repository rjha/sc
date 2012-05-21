<?php

    //sc/monitor/feedback.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $feedbackDao = new \com\indigloo\sc\dao\Feedback();

	$total = $feedbackDao->getTotalCount();
	$pageSize =	20;
	$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
	$feedbackDBRows = $feedbackDao->getPaged($paginator);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - feedback posted by users  </title>
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
                <div class="span12">
                <?php $activeTab = 'feedbacks'; include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    
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
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/feedback.php', $startId, $endId); ?>

        <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


