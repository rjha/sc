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
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>

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

    <body>

        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <?php
                        $options = array();
                        $options["group"] = true ;
                        $postView = \com\indigloo\sc\html\Post::createPostView($postDBRow,$options);
                        echo \com\indigloo\sc\html\Post::getHeader($postView);
                    ?>

                </div>
            </div>
            <div class="row">
                <div class="span9">
                    <?php
                        echo \com\indigloo\sc\html\Post::getGalleria($itemObj->title,$images);
                        echo \com\indigloo\sc\html\Post::getDetail($postView,$links,$siteDBRow);
                        echo \com\indigloo\sc\html\Post::getToolbar($loginIdInSession,$postDBRow['login_id'],$itemId);

                        //inject activity tile
                        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
                        $feedDataObj = $activityDao->getPostFeeds($itemId, 10);
                        $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                        $feedHtml = $htmlObj->getPostTile($feedDataObj);
                        echo \com\indigloo\sc\html\Post::getActivity($feedHtml);

                        \com\indigloo\sc\html\Comment::renderAll($commentDBRows);
                        include(APP_WEB_DIR . '/qa/inc/comment.inc');


                    ?>
                    <h3> explore 3mik </h3>
                    <div id="tiles">
                        <?php
                        foreach ($xrows as $xrow) {
                            echo \com\indigloo\sc\html\Post::getSmallTile($xrow);
                        }
                        ?>
                    </div> <!-- tiles -->


                </div>
                <div class="span3">


                </div>


            </div>


        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <!-- @todo : remove jquery validate -->
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>
        <script src="/3p/jquery/galleria/galleria-1.2.7.min.js"></script>

    <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>

    <script type="text/javascript">

        $(document).ready(function(){

            webgloo.sc.toolbar.add();
            webgloo.sc.home.addSmallTiles();
            webgloo.sc.item.addActions();

            Galleria.loadTheme('/3p/jquery/galleria/themes/classic/galleria.classic.min.js');
            Galleria.run('#galleria', {

                extend: function(options) {


                    // listen to when an image is shown
                    this.bind('image', function(e) {
                        // lets make galleria open a lightbox when clicking the main image:
                        $(e.imageTarget).click(this.proxy(function() {
                            this.openLightbox();
                        }));
                    });
                }


            });

            $('#play').click(function() {
                $('#galleria').data('galleria').play();

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


</body>
</html>
