<?php
    include("sc-app.inc");
    include(APP_CLASS_LOADER);

    use \com\indigloo\Util as Util;
    use com\indigloo\Url as Url;

?>

<!DOCTYPE html>
<html>

    <head>
        <title> jqplot test - 3mik.com </title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

       
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <link rel="stylesheet" type="text/css" href="/3p/jquery/jqplot/jquery.jqplot.min.css" />

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
                    <div id="chartdiv" style="height:450px;width:600px; "></div>
                </div>
            </div>


        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

      
    <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
    <script language="javascript" type="text/javascript" src="/3p/jquery/jqplot/jquery.jqplot.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var ajaxDataRenderer = function(url, plot, options) {
            var ret = null;
            $.ajax({
              async: false,
              url: url,
              dataType:"json",
              success: function(data) {
                ret = data;
              }
            });
            return ret;
          };
         
        // The url for our json data
        var jsonurl = "/test/chart/jqplot-data.php";

        var plot2 = $.jqplot('chartdiv', jsonurl,{
            title: "Sine Curve",
            seriesColors:["red"],
            dataRenderer: ajaxDataRenderer,
            grid: {
                drawGridLines: false
            },
            axes: {
                xaxis: {
                    show: false 
                }
            },
            dataRendererOptions: {
              unusedOptionalUrl: jsonurl
            }
          });

        });
    </script>


</body>
</html>
