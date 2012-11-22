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
    use \com\indigloo\sc\html\SocialGraph as GraphHtml ;

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $activityDao = new \com\indigloo\sc\dao\ActivityFeed() ;
    $feedDataObj = $activityDao->getUserFeeds($loginId,50);

    $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
    $followers = $socialGraphDao->getFollowers($loginId,5);
    $followings = $socialGraphDao->getFollowing($loginId,5);

    $followerUIOptions = array(
        "ui" => "feed",
        "more" => "/user/dashboard/follower.php", 
        "image" => true);

    $followingUIOptions = array(
        "ui" => "feed",
        "more" => "/user/dashboard/following.php", 
        "image" => true);

     $followersHtml = GraphHtml::getTable($loginId,$followers,1,$followerUIOptions);
     $followingsHtml = GraphHtml::getTable($loginId,$followings,2,$followingUIOptions);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> Activities - <?php echo $loginName; ?> </title>
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
                <div class="span6">
                    
                    <div class="feeds">
                        <?php

                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $html = $htmlObj->getHtml($feedDataObj);
                        echo $html ;

                        ?>
                    </div>

                </div>
                <div class="span4 offset1">
                    <div>
                        <span class="flickr-color b"> Followers </span>
                        <?php echo $followersHtml ; ?> 
                        <a href="/user/dashboard/follower.php"> view all &rarr; </a>
                    </div>
                    <div class="mt20">
                        <span class="flickr-color b"> Followings </span>
                        <?php echo $followingsHtml ; ?> 
                        <a href="/user/dashboard/following.php"> view all &rarr; </a>
                    </div>

                       
                

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
