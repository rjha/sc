<?php

    use \com\indigloo\sc\html\User as UserHtml ;
    use \com\indigloo\sc\html\Post as PostHtml ;
    use \com\indigloo\sc\html\SocialGraph as GraphHtml ;
    

    $headerHtml = UserHtml::getPubHeader($userDBRow);


    $content = PostHtml::getImageGrid($postDBRows);
    $count = $ucounters["post_count"]; 
    $options = array ("title" => "Items", "tab" => "items");
    $itemsHtml = UserHtml::getPubWrapper($pageBaseUrl,$count,$content,$options);

    $content = PostHtml::getImageGrid($likeDBRows);
    $count = $ucounters["like_count"];
    $options = array ("title" => "Likes", "tab" => "likes");
    $likesHtml = UserHtml::getPubWrapper($pageBaseUrl,$count,$content,$options);

    
    $content = GraphHtml::getTable($loginId,$followers,1,$followerUIOptions);
    $count = $ucounters["follower_count"];
    $options = array ("title" => "Followers", "tab" => "followers");
    $followersHtml = UserHtml::getPubWrapper($pageBaseUrl,$count,$content,$options);

    $content = GraphHtml::getTable($loginId,$followings,2,$followingUIOptions);
    $count = $ucounters["following_count"];
    $options = array ("title" => "Followings", "tab" => "followings");
    $followingsHtml = UserHtml::getPubWrapper($pageBaseUrl,$count,$content,$options);

    $htmlActivityObj = new \com\indigloo\sc\html\ActivityFeed();
    $activityHtml  = $htmlActivityObj->getHtml($feedDataObj);
    

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
                    <?php 
                        echo  $headerHtml; 
                        echo  $itemsHtml;  
                        echo  $likesHtml;  
                       
                    ?>
                    
                    <div class="row">
                        <div class="span6">
                            <?php
                                echo $followersHtml ;
                                echo $followingsHtml ;
                            ?>
                        </div>
                    </div>

                </div>

                <div class="span3 offset1">
                    <div class="feeds">
                        <?php  echo  $activityHtml; ?>
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
