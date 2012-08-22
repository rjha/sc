<?php
    //sc/user/dashboard/profile.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    //$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user exists with this login_id", E_USER_ERROR);
    }

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body class="pt120">
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span9 mh600">
                <?php echo \com\indigloo\sc\html\User::getProfile($gSessionLogin,$userDBRow) ; ?>
                </div>
                <div class="span3">
                </div>
            </div> <!-- row -->
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



