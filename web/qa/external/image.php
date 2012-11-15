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
    $qUrl = Url::tryBase64QueryParam("q","/");

?>

<!DOCTYPE html>
<html>

    <head>
        <title> share images from a webpage </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
       
        <style>
            
            #link-box {width:100%; }
            #ajax-message .error { color:red ; }
            #image-preview { padding-top:10px; }
            /* override default stack image padding */
            #image-preview .container { padding: 1px; }
            #next-container { 
                padding-left:10px;
                border-left:2px solid #ccc ;
                display: none;
            }

            .btn-container { padding-top: 40px;}
        </style>



    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">
            <div class="mt20">&nbsp;</div>
            <?php FormMessage::render(); ?>

            <div class="row">
                <div class="span7">
                    <div class="p10"> 
                        Type webpage URL and click on fetch ( or press Enter ) 
                    </div>
                    <input id="link-box" name="link" value="" />
                </div>
                <div class="span3 btn-container">
                    <button id="fetch-button" type="button" class="btn" value="Fetch">Fetch</button>
                    &nbsp;
                    <span id="next-container">
                        <a id="next-button" class="btn" href="#">Next</a>
                    </span>

                </div>
                <div id="form-wrapper">

                    <form  id="web-form1"  name="web-form1" action="/qa/external/router.php"  method="POST">
                        <input type="hidden" name="images_json" />
                        <input type="hidden" name="description" />
                        <input type="hidden" name="link" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                    </form>
                </div> <!-- form-wrapper -->

            </div> <!-- row:1 -->
            <div class="row">
                <div id="ajax-message" class="ml20 p20"> </div>
            </div> 

            <div class="row">

                <div id="image-preview" class="p20">
                    
                </div>
            </div> <!-- row:3 -->

        </div> <!-- container -->


        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){
                //webgloo.sc.ImageSelector.debug= true ;
                webgloo.sc.ImageSelector.attachEvents();

            });

        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>


    </body>
</html>

