<?php

    include('sc-app.inc');
    include(APP_CLASS_LOADER);

    //pull one random image
    $postDao = new \com\indigloo\sc\dao\Post();
    $rows = $postDao->getRandom(5);
    $tileHtml = '' ;
    if(sizeof($rows) > 0 ) {
        $tileHtml = \com\indigloo\sc\html\Post::getTile($rows[0]);
    }

?>

<!DOCTYPE html>
<html>

       <head>
       <title> Thanks for visiting 3mik </title>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>

        <div class="container mh800">
            <div class="row">  
                <div class="span3 offset2">
                    <?php echo $tileHtml ; ?>
                </div>

                <div class="span4 offset1">
                    <div class="noresults">
                        Thanks for visiting, <br>
                        see you soon.
                    </div>
                </div>
            </div>


        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            window.setTimeout(function() {window.location.href = 'http://www.3mik.com'; }, 6000);
        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
