<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\app\auth\Login as Login ;
    use \com\indigloo\app\api\Graph as GraphAPI ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    $loginId = Login::getLoginIdInSession();

    // get access token
    // get pages for this user using /me/accounts API

    $loginDao = new \com\indigloo\app\dao\Login();
    
    $accessToken = $loginDao->getToken($loginId);
    $pages = GraphAPI::getPages($accessToken);
    // save pages in session
    // after user consent - save/destroy the pages.

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
                    <h2>Show page information</h2>
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

