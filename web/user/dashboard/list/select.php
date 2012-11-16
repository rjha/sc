<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Constants as Constants;

    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\sc\auth\Login as Login;
    
    
    $gSessionLogin = Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $qparams = Url::getRequestQueryParams();
    $itemId = Util::getArrayKey($qparams, "item_id");
    $qUrl = base64_encode("/item/".$itemId) ;
    $fUrl = Url::current();

    $listDao = new \com\indigloo\sc\dao\Lists();
    $listRows = $listDao->getOnLoginId($loginId);
    $listHtml = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$itemId,$qUrl);
     
    //get widget html
    $postDao = new \com\indigloo\sc\dao\Post();
    $itemRow = $postDao->getOnItemId($itemId);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> Save item - <?php echo $loginName ; ?>  </title>
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
                        <h3> Save item </h3>
                    </div>
                </div>
            </div> <!-- page:header -->
           
            <?php FormMessage::render(); ?>

            <div class="row">
                <div class="span9 offset1">
                    <div id="page-message" class="hide-me"> </div>
                     <p class="muted">
                        To save this item, select a list below. You can also create a new 
                        list.
                    </p>
                    <?php echo \com\indigloo\sc\html\Post::getWidget($itemRow); ?>
                   
                    <div id="list-popup">
                        <?php echo $listHtml; ?> 
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
                webgloo.sc.Lists.init();

            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



