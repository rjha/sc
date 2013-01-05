<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
     
    use com\indigloo\ui\form\Message as FormMessage;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $stoken = Util::getMD5GUID();
    $gWeb->store("mik_state_token",$stoken);

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $host = Url::base();
    $fbCallback = $host."/app/browser/login-router.php" ;

    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email,manage_pages,publish_stream&state=".$stoken ;

    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - login page</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
        	<div class="row">
        		<div class="span6 offset3">
        			<h2> Browser Sign in page </h2>
                    <div class="p10"> <?php FormMessage::render(); ?> </div>
        			<a href="<?php echo $fbDialogUrl; ?>" class="btn-adbox"> Sign in</a>
        		</div>
        	</div>
        </div>
        
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

