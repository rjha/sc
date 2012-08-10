<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Url as Url ;

    $qUrl = Url::tryQueryParam('q');
    $qUrl = empty($qUrl) ? '/' : $qUrl;


?>

<!DOCTYPE html>
<html>

       <head>
        <title> Thank you for submitting a Post</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $qUrl; ?>'; }, 5000);
        </script>

    </head>

    <body>
        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/slim-toolbar.inc'); ?>
                </div>

            </div>
            
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
                           <a class="btn btn-primary" href="/"> Home Page </a>
                        </p>
                    </div>

                </div>
            </div>
        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
