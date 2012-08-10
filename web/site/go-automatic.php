<!DOCTYPE html>
<html>

       <head>
        <title> <?php echo $title; ?> </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $goUrl; ?>'; }, 5000);
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
                    <div class="row">
                        <div class="page-header">
                        <h2> <?php echo $header; ?> </h2>
                        </div>
                        <div>
                            <blockquote class="pull-right">
                                <span class="comment-text"> Redirecting. Just a moment please </span>
                                <span> <img src="/css/asset/sc/fb_loader.gif" alt="ajax loader" /></span>
                                <small>Click &quot;<?php echo $goText; ?>&quot; button if you do not want to wait.</small>
                            </blockquote>
                        </div>

                    </div>

                    <div class="row">
                        <div class="span2 offset6">
                            <a class="btn btn-primary" href="<?php echo $goUrl; ?>"><?php echo $goText; ?></a>
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
