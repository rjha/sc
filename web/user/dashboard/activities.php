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



    $options = array("ui" => "feed","image" => true);
    $followersData = GraphHtml::getTable($loginId,$followers,1,$options);
    $options = array(
        "link" => "/user/dashboard/follower.php",
        "size" => sizeof($followers),
        "title" => "Followers");
    $followersHtml = GraphHtml::getDashWrapper($followersData,$options);


    $options = array("ui" => "feed","image" => true);
    $followingsData = GraphHtml::getTable($loginId,$followings,2,$options);
    $options = array(
        "link" => "/user/dashboard/following.php",
        "size" => sizeof($followings),
        "title" => "Followings");

    $followingsHtml = GraphHtml::getDashWrapper($followingsData,$options);

    $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
    $activityHtml = $htmlObj->getHtml($feedDataObj);

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
                        <?php echo $activityHtml ; ?>
                    </div>

                </div>

                <div class="span4 offset1">
                    <?php echo $followersHtml ; ?> 
                    <?php echo $followingsHtml ; ?> 

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
