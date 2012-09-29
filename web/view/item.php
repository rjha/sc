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


        <script>

            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();

        </script>



    </head>

    <body class="dark-body">
        <div id="fb-root"></div>

        <script>

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));

        </script>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>

        <div class="container mh800">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
            
            <div class="row">
                <div class="span9 wbg">
                    <div id="item-page">
                    <?php

                        echo \com\indigloo\sc\html\Post::getHeader($postView,$loginIdInSession);
                        echo \com\indigloo\sc\html\Post::getFancybox($itemObj->title,$postView->images);
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

                        <?php echo \com\indigloo\sc\html\Post::getSitePanel($siteMetaRow,$sitePostRows); ?>
                        
                        <div class="mt20">
                            <blockquote>
                                 <span class="faded-text b"> Related items</span>
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


                    <div class="section" style="overflow:visible">
                        <div class="fb-like pb10" data-href="<?php echo $itemObj->netLink;?>" data-send="false" data-layout="button_count" data-width="225" data-show-faces="false">
                        </div>
                        <div class="pb10">
                            <a href="https://twitter.com/share" class="twitter-share-button" data-via="3mikindia" data-count="none">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                        </div>

                        <!-- g+1 button -->
                        <div class="g-plusone" data-size="tall" data-href="<?php echo $itemObj->netLink;?>" data-annotation="none" data-width="200"></div>

                    </div>


                </div>


            </div>

            <div id="feedback" class="vertical">
                <a href="/site/contact.php">
                    <br>
                    F e e d b a c k
                </a>
            </div>  <!-- feedback -->


        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                webgloo.sc.toolbar.add();
                webgloo.sc.item.addActions();

                $("a.gallery").fancybox();

                var $container = $('#tiles');

                $container.imagesLoaded(function(){
                    $container.isotope({
                        itemSelector : '.stamp',
                        layoutMode : 'masonry'
                    });

                });

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });

            });


        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
