<?php
    //sc/user/dashboard/activities.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\ui\Filter as Filter;
    
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user record found for given login_id", E_USER_ERROR);
    }

    $activityDao = new \com\indigloo\sc\dao\ActivityFeed() ;
    $feedDataObj = $activityDao->getUserFeeds($loginId,50);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
            <div class="row">
                <div class="span9">
                    <div class="page-header">
                        <div class="faded-text">Activities</div>
                    </div>
                </div>
                
                <div class="span9 mh600">
                    <div class="feeds">
                    <?php

                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $html = $htmlObj->getHtml($feedDataObj);
                        echo $html ;

                    ?>
                    </div>

                </div>
                <div class="span3">
                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
            });

        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
