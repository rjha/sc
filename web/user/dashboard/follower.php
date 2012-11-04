<?php
    //sc/user/dashboard/following.php
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
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }
    
    $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
    $followers = $socialGraphDao->getFollowers($loginId);


?>


<!DOCTYPE html>
<html>

    <head>
        <title> followers <?php echo $loginName; ?>  </title>
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
                <div class="span12">
                    <div class="page-header">
                        <h4> Followers </h4>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="span8 offset1">
                    <div id="uwidgets">
                    <?php 
                        foreach($followers as $follower){
                            echo \com\indigloo\sc\html\SocialGraph::getWidget($loginId,$follower); 
                        }

                    ?>
                    </div>
                </div>
                
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){
                webgloo.sc.toolbar.add();
                webgloo.sc.item.addActions();

            }) ;


        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
