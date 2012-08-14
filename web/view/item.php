<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $itemObj->title; ?> - 3mik.com </title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription; ?>">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link rel="canonical" href="<?php echo $itemObj->link; ?>">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

        <!-- opengraph curry -->
        <meta property="og:title" content="<?php echo $itemObj->title ?>"/>
        <meta property="og:image" content="<?php echo $itemObj->picture ?>"/>
        <meta property="og:description" content="<?php echo $itemObj->description; ?>"/>


        <!-- g+1 async js -->
        <script type="text/javascript">
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
        </script>


    </head>

    <body class="dark-body">

        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>


            <div class="row">
                <div class="span9 wbg">
                    <div id="item-page">
                    <?php

                        $options = array();
                        $options["group"] = true ;
                        $postView = \com\indigloo\sc\html\Post::createPostView($postDBRow,$options);
                        echo \com\indigloo\sc\html\Post::getHeader($postView,$loginIdInSession);

                        echo \com\indigloo\sc\html\Post::getFancybox($itemObj->title,$images);
                        echo \com\indigloo\sc\html\Post::getDetail($postView,$links);

                        //inject activity tile
                        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
                        $feedDataObj = $activityDao->getPostFeeds($itemId, 10);
                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $feedHtml = $htmlObj->getPostTile($feedDataObj);
                        $commentHtml = \com\indigloo\sc\html\Comment::getFeedHtml($commentDBRows);
                        echo \com\indigloo\sc\html\Post::getActivity($feedHtml,$commentHtml);

                        include(APP_WEB_DIR . '/qa/inc/comment.inc');

                    ?>
                    </div>
                    <div class="section">

                        <?php echo \com\indigloo\sc\html\Post::getMoreLinks($postView,$siteDBRow); ?>
                        <div class="mt20">
                            <blockquote>
                                 <span class="faded-text"> Related items</span>
                            </blockquote>


                            <div id="tiles">
                                <?php
                                foreach ($xrows as $xrow) {
                                    echo \com\indigloo\sc\html\Post::getSmallTile($xrow);
                                }
                                ?>
                            </div> <!-- item:tiles -->
                        </div>
                    </div>


                </div>
                <div class="span3 wbg">

                    <?php echo \com\indigloo\sc\html\Post::getGroups($postView); ?>

                    <div class="section social-buttons">
                        <div class="pb10">
                            <a href="https://twitter.com/share" class="twitter-share-button" data-via="3mikindia" data-count="none" data-size="large">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                        </div>

                        <div class="pb10">
                            <a href="#" class="zocial facebook" id="share-facebook">share on facebook</a>
                        </div>
                        <div>
                               <a href="#" class="zocial googleplus" id="share-google">share on google+</a>
                        </div>

                    </div>


                </div>


            </div>


        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                webgloo.sc.toolbar.add();
                webgloo.sc.item.addActions();

                $("a.gallery").fancybox();

                var $container = $('#tiles');
                $container.imagesLoaded(function(){
                    $container.masonry({
                        itemSelector : '.stamp'
                    });
                });

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });

                webgloo.sc.item.openShareWindow = function(title,url) {

                    var popupWidth = 500 ;
                    var popupHeight = 375 ;
                    var xPosition=($(window).width()-popupWidth)/2;
                    var yPosition=($(window).height()-popupHeight)/2;

                    var popupOptions = "width=" + popupWidth +
                        ",height=" + popupHeight +
                        ",left=" + xPosition +
                        ",top=" + yPosition +
                        "menubar=no,toolbar=no,resizable=yes,scrollbars=yes";

                    window.open(url,title,popupOptions);

                } ;



                $("#share-facebook").click(function(event) {

                    var itemObj = {};
                    var strItemObj = '<?php echo $strItemObj; ?>' ;

                    try{
                        itemObj = JSON.parse(strItemObj) ;
                    } catch(ex) {
                        console.log("Error parsing the item data json");
                        return ;
                    }

                    var fbUrl = "http://www.facebook.com/dialog/feed?app_id=" + itemObj.appId +
                        "&display=popup" +
                        "&redirect_uri=" + encodeURIComponent(itemObj.callback) +
                        "&picture=" + itemObj.picture +
                        "&link=" + encodeURIComponent(itemObj.link) +
                        "&name=" + encodeURIComponent(itemObj.title) +
                        "&description=" + encodeURIComponent(itemObj.description) ;

                    webgloo.sc.item.openShareWindow("Share on Facebook", fbUrl);

                });

                $("#share-google").click(function(event) {

                    var itemObj = {};
                    var strItemObj = '<?php echo $strItemObj; ?>' ;

                    try{
                        itemObj = JSON.parse(strItemObj) ;
                    } catch(ex) {
                        console.log("Error parsing the item data json");
                        return ;
                    }

                    var googleUrl = "https://plus.google.com/share?url=" + encodeURIComponent(itemObj.netLink) ;
                    webgloo.sc.item.openShareWindow("Share on Google+", googleUrl);


                });

            });

        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
