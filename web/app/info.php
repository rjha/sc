<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\app\auth\Login as Login ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    $loginId = Login::tryLoginIdInSession();

    // get access token
    // get pages for this user
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> User information page</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <h2> User info page </h2>
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

