<?php

    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;


    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
    $message =  Url::tryQueryParam("g_message");

    //decode message.
    if(!empty($message)) {
        $message = base64_decode($message);
    }

    $message = (($message === FALSE) || empty($message)) ? "No Message!" : $message ;

?>

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik - complete session action</title>
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
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                </div>
            </div>


            <div class="row">
                <div class="span9">
                    <div class="row">
                        <div class="hero-unit">
                            <h1>Redirecting...</h1>
                        </div>
                        <div class="p20">
                            <img src="/css/images/ajax_loader.gif" alt="ajax loader" />
                        </div>
                        <div class="page-header"> </div>
                        <div>
                            <blockquote class="pull-right">
                                <p>
                                    <?php echo $message; ?>
                                </p>
                                <small>Click the Go back button if you do not want to wait.</small>
                            </blockquote>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span2 offset6">
                            <a class="btn btn-primary" href="<?php echo $qUrl; ?>">Go Back</a>
                        </div>
                    </div>


                </div> <!-- span9 -->
            </div>
        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
