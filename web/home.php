<!DOCTYPE html>
<html>

    <head>
    <title> <?php echo $pageTitle; ?>  </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>


    </head>

     <body class="dark-body2">
        <div class="container">
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
                    <div id="tiles">

                        <?php
                            $count = 0 ;
                            foreach($this->homeDBRows as $postDBRow) {
                                $count++ ;
                                if($gpage == 1 && $count == 4) {
                                    //inject activity tile
                                    $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
                                    $feedDataObj = $activityDao->getGlobalFeeds(10);
                                    $htmlObj = new \com\indigloo\sc\html\ActivityFeed();
                                    $html = $htmlObj->getHomeTile($feedDataObj);
                                    echo $html ;
                                }

                                $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                echo $html ;

                            }

                        ?>

                    </div><!-- tiles -->
                    <hr>
                    <ul class="pager">
                        <li> <a id="pager" href="<?php echo $nextPageUrl ?>">Next &rarr;</a></li>
                    </ul>


                </div>
            </div> <!-- row -->

            <div id="scroll-image" style="margin-left:800px; height:120px; width:160px;"> </div>
        </div>  <!-- container -->

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/infinite/jquery.infinitescroll.js"> </script>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>


        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){
                var $container = $('#tiles');

                $container.imagesLoaded(function(){
                    $container.masonry({
                        itemSelector : '.tile'
                    });
                });

                $container.infinitescroll(
                    {
                        navSelector  	: "a#pager:last",
                        nextSelector 	: "a#pager:last",
                        itemSelector : ".tile",
                        bufferPx : 100 ,
                        loading : {
                            selector : "#scroll-image",
                            img : "/css/images/6RMhx.gif",
                            finishedMsg : "<b> You have reached the end of this page </b>"
                        }
                        /*
                         *
                         ,

                        pathParse: function(path,page_num) {
                            var opath = '<?php echo $nextPageUrl ?>'  ;
                            return new Array(opath) ;
                        } */

                    },
                    function( newElements ) {
                        // hide new items while they are loading
                        var $newElems = $( newElements ).css({ opacity: 0 });
                        // ensure that images load before adding to masonry layout
                        $newElems.imagesLoaded(function(){
                        // show elems now they're ready
                        $newElems.animate({ opacity: 1 });
                        $container.masonry( 'appended', $newElems, true );
                        });
                    }
                );



                //show options on hover
                $('.tile .options').hide();
                $('.tile').mouseenter(function() {$(this).find('.options').toggle();});
                $('.tile').mouseleave(function() {$(this).find('.options').toggle();});

                //Add item toolbar actions
                webgloo.sc.item.addActions();


            });


        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
