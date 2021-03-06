<?php

    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

?>

<!DOCTYPE html>
<html>

       <head>
        <title> Flash detection page </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/yui3/grids-min.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/swfobject2.2/swfobject.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){

                //swfupload minimum flash req. can be seen inside swfupload.js file
                if(swfobject.hasFlashPlayerVersion("9.0.28")){
                     var playerVersion = swfobject.getFlashPlayerVersion();
                     $("#major-no").html("major: " + playerVersion.major);
                     $("#minor-no").html("minor: " + playerVersion.minor);
                     $("#release-no").html("release: " + playerVersion.release);



                }else {
                    $("#major-no").html("Required flash version not found");
                }

            });

        </script>

    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div id="body-wrapper">

              
                <div id="bd">

                    <div class="yui3-g">

                        <div class="yui3-u-2-3">
                            <div id="content">

                            Flash detection page
                            <p id="major-no"> </p>
                            <p id="minor-no"> </p>
                            <p id="release-no"> </p>

                        </div> <!-- content -->

                         <div class="yui3-u-1-3">
                            <!-- sidebar -->
                        </div>
                    </div>
                </div>

                </div> <!-- bd -->

        </div> <!-- body wrapper -->

        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>

    </body>
</html>
