<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;

    use \com\indigloo\ui\Filter as Filter;

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name ;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    //$userDao = new \com\indigloo\sc\dao\User();
    //$counters = $userDao->getCounters($loginId);

?>


<!DOCTYPE html>
<html>

    <head>
        <title>  Dashboard - <?php echo $loginName ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">

            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                 <div class="span12">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>

            </div>
            <?php FormMessage::render(); ?>
            

            <div class="row">
               
                <div class="span8 offset1">
                     Tiles
                </div>
                <div class="span3">
                    
                </div>
               
            </div>
        </div> <!-- container -->
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        s
        <script>
            
            $(document).ready(function(){
                //fix twitter bootstrap alerts
                webgloo.sc.util.fixAlert();
                webgloo.sc.toolbar.add();
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



