<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> - 3mik.com </title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link rel="canonical" href="<?php echo $itemObj->link; ?>">
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">

        <!-- opengraph curry -->
        <meta property="og:title" content="<?php echo $itemObj->name ?>"/>
        <meta property="og:image" content="<?php echo $itemObj->picture ?>"/>
        <meta property="og:description" content="<?php echo $itemObj->description; ?>"/>

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <link rel="stylesheet" type="text/css" href="/3p/fancybox/jquery.fancybox-1.3.4.css"></script>
        <script type="text/javascript" src="/3p/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?> 

        <script type="text/javascript">

            $(document).ready(function(){

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

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });

                webgloo.sc.home.addNavGroups();
                webgloo.sc.home.addSmallTiles();
                webgloo.sc.item.addActions();

                $("a.gallery").fancybox();

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
                        "&name=" + encodeURIComponent(itemObj.name) +
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

        <!-- g+1 async js -->
        <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>


    </head>

     <body>

        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                    <?php include(APP_WEB_DIR . '/inc/browser.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2> <?php echo $pageTitle; ?>  </h2>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="span8">
                    <?php
                        echo \com\indigloo\sc\html\Post::getGallery($images) ;
                        echo \com\indigloo\sc\html\Post::getLinks($links,$siteDBRow) ;
                        echo \com\indigloo\sc\html\Post::getDetail($postDBRow) ;
                        \com\indigloo\sc\html\Comment::renderAll($commentDBRows);
                        include(APP_WEB_DIR.'/qa/inc/comment.inc') ;
                    ?>

                     <div id="item-tiles">
                        <h3> explore 3mik </h3>
                        <div id="tiles">
                            <?php
                            foreach($xrows as $xrow) {
                                echo \com\indigloo\sc\html\Post::getSmallTile($xrow) ;
                            }
                            ?>
                        </div> <!-- tiles -->
                    </div>
                </div>

                <div class="span4">
                    <?php
                        echo \com\indigloo\sc\html\Post::getGroups($postDBRow) ;
                        //Action toolbar
                        echo \com\indigloo\sc\html\Post::getToolbar($itemId,$loginId,$postDBRow['login_id']) ;
                    ?>
                    <div class="feeds mt20">
                    <?php
                        //inject activity tile
                        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
                        $feedDataObj = $activityDao->getPostFeeds($itemId,10);
                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $html = $htmlObj->getPostTile($feedDataObj);
                        echo $html ;

                     ?>
                    </div> <!-- feeds -->

              </div>
            </div> <!-- row -->

        </div> <!-- container -->

    <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
    </div>

    </body>
</html>
