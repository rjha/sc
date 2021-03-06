<?php

    //sc/site/feed.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    $feedDao = new \com\indigloo\sc\dao\Activity();
    $feedDataObj = $feedDao->getGlobalFeeds(50);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Activity feeds  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h3> Activity feed </h3>
                    </div>
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
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(function(){
                webgloo.sc.toolbar.add();
            });
        </script>


        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


