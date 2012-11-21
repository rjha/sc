<?php

    $htmlHeader = \com\indigloo\sc\html\User::getPublic($userDBRow);
    $htmlPosts = \com\indigloo\sc\html\Post::getImageGrid($postDBRows);
    $htmlFollowers = \com\indigloo\sc\html\SocialGraph::getTable($loginId,$followers,1,$followerUIOptions);
    $htmlFollowings = \com\indigloo\sc\html\SocialGraph::getTable($loginId,$followings,2,$followingUIOptions);

    $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
    $htmlActivity  = $htmlObj->getHtml($feedDataObj);
    $htmlLikes = \com\indigloo\sc\html\Post::getImageGrid($likeDBRows);


?>


<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <link rel="stylesheet" type="text/css" href="/css/extra.css" >  

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
            
            <div class="row">

                <div class="span7">
                    <?php echo  $htmlHeader; ?>
                    <div class="p10">
                        <h5>
                            Items (<?php echo $ucounters["post_count"] ?>)
                            &nbsp;&nbsp;
                            <a class="b" href="<?php echo $pageBaseUrl?>?show=items">view all items</a>
                        </h5>
                        <?php echo  $htmlPosts; ?>
                        
                    </div>

                    <div class="p10">
                        <h5>
                            Likes (<?php echo $ucounters["like_count"] ?>) 
                            &nbsp;&nbsp;
                            <a class="b" href="<?php echo $pageBaseUrl?>?show=likes">view all likes</a>
                        </h5>
                        <?php echo  $htmlLikes; ?>
                    </div>

                    <div class="row">
                        <div class="span6">
                            <?php echo  $htmlFollowers ;  ?>
                            <?php echo  $htmlFollowings; ?>
                        </div>
                    </div>

                </div>

                <div class="span3 offset1">
                    <div class="feeds">
                        <?php  echo  $htmlActivity; ?>
                    </div>
                </div>

            </div> <!-- row -->


            <div id="scroll-loading"> </div>

        </div>  <!-- container -->

        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
             
            $(function(){

                //Add item toolbar actions
                webgloo.sc.item.addActions();
                webgloo.sc.toolbar.add();

            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>



    </body>
</html>
