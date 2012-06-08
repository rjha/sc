<?php
    //sc/user/dashboard/following.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\ui\Filter as Filter;
    //$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
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

    $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
    $followings = $socialGraphDao->getFollowing($loginId);
    
    
?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
         <script type="text/javascript">

            $(document).ready(function(){
                 webgloo.sc.item.addActions();
                
            }) ;
            

        </script>
    </head>

    <body>
         <div id="block-spinner"> </div>
        <div id="simple-popup">
            <div id="content"> </div>
            <div class="panel-footer">
                <div class="floatr">press Esc or&nbsp;<a id="simple-popup-close" href="">close&nbsp;<i class="icon-remove"></i></a> </div>
            </div>
        </div> <!-- simple popup -->
        
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                     <?php  include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="/user/dashboard/following.php">Following</a>
                        </li>
                         <li class="">
                            <a href="/user/dashboard/follower.php">Followers</a>
                        </li>
                        
                    </ul>
                </div>
                <div class="span7 mh600">
                    <?php echo \com\indigloo\sc\html\SocialGraph::getFollowingHtml($loginId,$followings); ?>  
                </div>
          
            </div>
        </div> <!-- container -->

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
