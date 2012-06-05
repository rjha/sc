<?php

    include('sc-app.inc');
    include(APP_CLASS_LOADER);

    //pull one random image
    $postDao = new \com\indigloo\sc\dao\Post();
    $rows = $postDao->getRandom(5);
    $tileHtml = \com\indigloo\sc\html\Post::getTile($rows[2]);


?>

<!DOCTYPE html>
<html>

       <head>
       <title> Thanks for visiting 3mik </title>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 mh600">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                    
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

            </div>


        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
