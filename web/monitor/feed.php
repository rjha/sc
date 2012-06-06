<?php

    //sc/monitor/feedback.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    //$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
    $feedDataObj = $feedDao->getGlobal();

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Activity feeds  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>



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
                <?php $activeTab = 'feeds'; include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    <div class="feeds">
                        <?php
                            $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                            $html = $htmlObj->getHtml($feedDataObj);
                            echo $html ;

                        ?>
                    </div> <!-- feeds -->
                    
                </div>
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


