<?php

    //sc/monitor/feed.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    $feedDao = new \com\indigloo\sc\dao\Activity();
    $feedDataObj = $feedDao->getGlobalFeeds(100);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Activity feeds  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>


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
                        <h2>Activities</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    <div class="feeds">
                        <?php
                            $htmlObj = new \com\indigloo\sc\html\Activity();
                            $html = $htmlObj->getHtml($feedDataObj);
                            echo $html ;

                        ?>
                    </div> <!-- feeds -->

                </div>
                
            </div>
        </div> <!-- container -->

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


