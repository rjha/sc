<?php
    //sc/user/dashboard/profile.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user exists with this login_id", E_USER_ERROR);
    }

    $passwordUrl = '' ;
    if(Login::hasMikLogin()) {
        $passwordUrl = '<a class="btn-flat" href="/user/account/change-password.php">Change password</a>';
    }

?>


<!DOCTYPE html>
<html>

    <head>
        <title> profile - <?php echo $loginName; ?>  </title>
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
            <div class="row">
                <div class="span11 offset1">
                    <div class="page-header">
                        <span style="padding-left:20px;padding-right:20px;">Profile </span>
                        <span>
                            <a class="btn-flat" href="/user/dashboard/mails.php">Mail preferences</a>
                        </span>
                        <span>
                          <?php echo $passwordUrl; ?>
                        </span>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span6 offset1">
                    <?php echo \com\indigloo\sc\html\User::getProfile($gSessionLogin,$userDBRow) ; ?>
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



