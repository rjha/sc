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
    use \com\indigloo\sc\util\PseudoId;

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $pubId = PseudoId::encode($loginId);
    $homeUrl = Url::base();
    $pubUrl = $homeUrl."/pub/user/".$pubId ;


    $loginName = $gSessionLogin->name ;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $analyticDao = new \com\indigloo\sc\dao\Analytic();
    $counters = $analyticDao->getUserCounters($loginId);

    $activityDao = new \com\indigloo\sc\dao\ActivityFeed() ;
    $feedDataObj = $activityDao->getUserFeeds($loginId,12);

    //suggestions are editor picks right now
    $postDao = new \com\indigloo\sc\dao\Post();
    //post featured filter
    $filters = array();
    $model = new \com\indigloo\sc\model\Post();
    $filter = new Filter($model);
    $filter->add($model::FEATURED,Filter::EQ,TRUE);
    array_push($filters,$filter);
    $postDBRows = $postDao->getPosts(10,$filters);


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
                <div class="span6 offset1">
                    <div class="">
                        <h2> Hello, <?php echo $loginName; ?> </h2>
                       

                    </div>

                </div>

            </div>

            <div class="row">
               
                <div class="span6">
                    <h5> Account</h5>
                    <hr>
                    <p class="muted">
                        Your dashboard provides a snapshot of your account
                        and what is happening in your network. Just click 
                        on any of the links to see details.
                    </p>
                    
                    
                    <?php echo \com\indigloo\sc\html\User::getCounters($counters); ?> 
                    <div class="clear"> </div>
                    <div class="section">
                        <span class="faded-text">check your public page  </span>
                        <span style="padding-left:20px;">
                        <a href="<?php echo $pubUrl ?>" class="b" target="_blank"><?php echo $pubUrl; ?></a>
                        </span>

                    </div>
                    
                    

                    <div> 
                        <h5> Suggestions </h5>
                        <hr>
                        <div id="tiles">
                        <?php
                            foreach($postDBRows as $postDBRow) {
                                $html = \com\indigloo\sc\html\Post::getSmallTile($postDBRow);
                                echo $html ;
                            }
                        ?>
                        </div>
                    </div>
                </div>

               

                <div class="span4 offset2">
                <div class="p10"> <a href="<?php echo $homeUrl; ?>">Go to www.3mik.com</a> </div> 
                    <ul class="breadcrumb">
                        <li class="active">Need help ?</li>
                        <li>
                            <a class="help-popup" rel = "dashboard.item.create" href="#">upload items</a> 
                            <span class="divider">/</span>
                        </li>
                        <li>
                            <a class="help-popup" rel = "dashboard.list.create" href="#">create lists</a> 
                            <span class="divider">/</span>
                        </li>
                        <li>
                            <a class="help-popup" rel = "dashboard.item.save" href="#">save items</a> 
                        </li>
                    </ul>

                    
                    <hr>
                    <div class="feeds">
                    <?php

                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $html = $htmlObj->getHtml($feedDataObj);
                        echo $html ;

                        ?>
                    </div>
                </div>
                
               
            </div>
        </div> <!-- container -->
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        <script>
            
            $(document).ready(function(){
                //fix twitter bootstrap alerts
                webgloo.sc.dashboard.fixAlert();
                webgloo.sc.toolbar.add();

                $("a.help-popup").click(function(event) {
                    var helpKey = $(this).attr("rel");
                    webgloo.sc.SimplePopup.init();
                    targetUrl = "/site/help/popup.php?hkey=" + helpKey;
                    webgloo.sc.SimplePopup.load(targetUrl);
                });

                var $container = $('#tiles');

                $container.imagesLoaded(function(){
                    $container.isotope({
                        itemSelector : '.stamp',
                        layoutMode : 'masonry'
                    });

                });
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



