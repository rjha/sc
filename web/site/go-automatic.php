<!DOCTYPE html>
<html>

       <head>
        <title> <?php echo $title; ?> </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
            
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
                            <a class="btn gBtnUp" href="<?php echo $goUrl; ?>"><?php echo $goText; ?></a>
                        </div>
                    </div>

                </div> <!-- span9 -->

            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $goUrl; ?>'; }, 5000);
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
