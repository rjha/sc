<?php

    //sc/site/feed.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    $feedDao = new \com\indigloo\sc\dao\ActivityFeed();
    $feedDataObj = $feedDao->getGlobal(100);

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
                <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR. '/inc/banner.inc'); ?>
                <?php include(APP_WEB_DIR. '/inc/browser.inc'); ?>
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


