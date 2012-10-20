<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    use com\indigloo\ui\form\Message as FormMessage;

   
    //Facebook OAuth2
    $fbAppId = Config::getInstance()->get_value("facebook.app.id");

    $host = Url::base();
    $fbCallback = $host."/callback/fb2.php" ;


    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email,publish_stream&display=popup" ;

    $fbDialogUrl2 = "https://www.facebook.com/dialog/apprequests?app_id=".$fbAppId ;
    $fbDialogUrl2 .= "message=Facebook%20Dialogs%20are%20so%20easy!&redirect_uri=".urlencode($fbCallback) ;


?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - login page</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        
        <div class="container mh800">
            <div style="margin-top:100px;"> &nbsp; </div> <!-- row:1 -->
                
            <div class="row mt20">
                <div class="span4 offset2">
                    <div class="social-buttons floatr">
                        <div class="p10">
                            <a  class="zocial facebook" href="<?php echo $fbDialogUrl ?>">invite with Facebook</a>
                        </div>
                         <div class="p10">
                            <a  class="zocial facebook" href="<?php echo $fbDialogUrl2 ?>">App Requests with Facebook</a>
                        </div>

                       
                    </div>

                </div> <!-- span3 -->


        </div> <!-- row:2 -->


       </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.social = webgloo.sc.social || {};

                webgloo.sc.social.openDialog = function(title,url,popupWidth,popupHeight) {

                    var xPosition=($(window).width()-popupWidth)/2;
                    var yPosition=($(window).height()-popupHeight)/2;

                    var popupOptions = "width=" + popupWidth +
                        ",height=" + popupHeight +
                        ",left=" + xPosition +
                        ",top=" + yPosition +
                        "menubar=no,toolbar=no,resizable=yes,scrollbars=yes";

                    window.open(url,title,popupOptions);

                } ;

                $("#facebook-permission").click(function(event) {
                    var fbUrl = '<?php echo $fbDialogUrl; ?>' ;
                    webgloo.sc.social.openDialog("Request for publishing permissions", fbUrl,500,375);
                });

            });

        </script>

        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>

    </body>
</html>
