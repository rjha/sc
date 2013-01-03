<!DOCTYPE html>
<html>
    
    <head>
        <title> <?php echo $itemObj->title; ?> - 3mik.com </title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $itemObj->description; ?>">

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
        <style>
            /* @hardcoded @inpage */
            .toolbar {
                border-top : 0px;
                border-bottom : 1px solid #d5d5d5;
            }
        </style>

    </head>

    <body>
        
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>

        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
             
            <div class="row">
                <?php echo \com\indigloo\sc\html\Site::formMessage(); ?>

                <div class="span9 wbg">
                   
                    <div id="page-message" class="hide-me"> </div>
                    <div id="item-page">
                   
                    <?php

                        echo \com\indigloo\sc\html\Post::getHeader($postView,$loginIdInSession);
                        echo \com\indigloo\sc\html\Post::getFancybox($itemObj->title,$postView->images);
                        echo \com\indigloo\sc\html\Post::getDetail($postView,$links);
                       
                        $likeHtml = \com\indigloo\sc\html\Post::getLikes($likeDBRows);
                        $commentHtml = '' ;
                        echo \com\indigloo\sc\html\Post::getActivity($likeHtml,$commentHtml);

                    ?>
                    </div>
                    

                </div>
                <div class="span3 wbg">
                    
                    <?php echo \com\indigloo\sc\html\Post::getUserPanel($postView,$loginIdInSession); ?>
                    <?php echo \com\indigloo\sc\html\Post::getGroups($postView); ?>
                   
                </div>

            </div>

        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){

                //isotope masonry
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

                $("a.gallery").fancybox();

                webgloo.sc.toolbar.add();
                webgloo.sc.item.addActions();
                webgloo.sc.dashboard.fixAlert();
                webgloo.sc.Lists.init();

                <?php if($gRegistrationPopup) { ?>
                    var targetUrl = "/user/popup/join-now.php" ;
                    webgloo.sc.SimplePopup.init();
                    webgloo.sc.SimplePopup.load(targetUrl);
                <?php } ?>

            });


        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
