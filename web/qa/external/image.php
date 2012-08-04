<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;

    $fUrl = Url::current();
    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;


?>

<!DOCTYPE html>
<html>

    <head>
        <title> share images from a webpage </title>

        <meta charset="utf-8">

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>


        <style>
            /*@todo move page specific styles to css file */
            #step1-container { margin-top:10px; padding:10px; }
            #step2-container { margin-top:10px; padding:10px; border-left:1px solid #f7f7f7;}

            #link-box {width:280px; }
            #fetch-button {width:60px; height:28px; margin-bottom:10px;}

            #ajax-message .normal {}
            #ajax-message .error { color:red ; }

            #stack { margin-top:40px; }

            /* override default stack image padding */
            div .stackImage { padding: 1px; }

        </style>



    </head>

    <body>
        <div class="container mh600">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/slim-toolbar.inc'); ?>
                </div>
            </div>

            <div class="hr"> </div>
            <?php FormMessage::render(); ?>

            <div class="row">
                <div class="span6">
                    <div id="step1-container">
                        <span class="badge badge-warning">Step1</span>
                        Type webpage URL and click fetch ( or press Enter )
                        <br>
                        <br>
                        <input id="link-box" name="link" value="" />
                        <button id="fetch-button" type="button" class="btn" value="Fetch">Fetch</button>
                    </div>

                </div> <!-- span -->
                <div class="span6">
                    <div id="step2-container">
                        <p>
                            <span class="badge badge-warning">Step2</span>
                            Place your mouse over an image to select it.
                            Please click Next after selecting images.
                        </p>
                        <ul class="pager">
                            <li> <a id="next-button" href="#">Next&nbsp;&rarr;</a> </li>
                        </ul>

                        <form  id="web-form1"  name="web-form1" action="/qa/external/router.php"  method="POST">
                            <input type="hidden" name="images_json" />
                            <input type="hidden" name="description" />
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div> <!-- step2-container -->

                </div>

            </div><!-- row:1 -->

            <div class="hr"> </div>
            <div id="ajax-message" class="ml20"> </div>

            <div class="row">
                <div id="stack">
                    <div class="images p10"> </div>
                </div>
            </div> <!-- row:2 -->

        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>


        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.ajaxQueue.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){
                //webgloo.sc.ImageSelector.debug= true ;
                webgloo.sc.ImageSelector.attachEvents();

            });

        </script>


    </body>
</html>

