<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    $qUrl = \com\indigloo\Url::tryBase64QueryParam('q', '/');
    $qUrl = base64_decode($qUrl);

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Thank you for submitting a Post</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
            
            <div class="row">
                <div class="span9">


                    <div class="page-header">
                        <h2> Thanks for submitting your post. Redirecting ... </h2>
                    </div>

                    <div class="p20">
                        <img src="/css/asset/sc/fb_loader.gif" alt="ajax loader" />
                    </div>

                    <div class="well">
                        <p class="help-text">
                           <a class="btn b" href="/"> Home Page </a>
                        </p>
                    </div>

                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $qUrl; ?>'; }, 5000);
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
