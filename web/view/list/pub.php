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
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>

            <div class="row">
                <div class="span12">
                   <?php 
                    if($gpage == 1 ) {
                        echo \com\indigloo\sc\html\Lists::getPubHeader($listDBRow,$userDBRow); 
                    }
                    ?>
                </div>
            </div> <!-- page:header -->

            
            <div class="row">

                <div class="span12">
                    <div id="tiles">
                    <?php

                        $startId = NULL;
                        $endId = NULL ;
                        $gNumRecords = sizeof($itemDBRows);

                        if( $gNumRecords> 0 ) {
                            $startId = $itemDBRows[0]["sort_id"] ;
                            $endId =   $itemDBRows[$gNumRecords-1]["sort_id"] ;
                            foreach($itemDBRows as $itemDBRow) {
                                $html = \com\indigloo\sc\html\Post::getListTile($itemDBRow);
                                echo $html ;

                            }
                        }else {
                            $message = "No items in list " ;
                            echo \com\indigloo\sc\html\Site::getNoResult($message,$options);

                        }

                    ?>

                    </div><!-- tiles -->

                    <hr>


                </div>
            </div> <!-- row -->


            <div id="scroll-loading"> </div>

        </div>  <!-- container -->

        <?php $paginator->render($pageBaseUrl,$startId,$endId,$gNumRecords);  ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            /* column width = css width + margin */
            $(function(){

                //show options on hover
                $('.tile .options').hide();

                function add_tile_options () {
                    $('.tile').live("mouseenter", function() {$(this).find('.options').show();});
                    $('.tile').live("mouseleave", function() {$(this).find('.options').hide();});
                }

                var $container = $('#tiles');

                $container.imagesLoaded(function(){
                    $container.isotope({
                        itemSelector : '.tile',
                        layoutMode : 'masonry',
                        onLayout : function( $elems, instance ) {
                            add_tile_options();
                        }                   
                    });

                });

                $container.infinitescroll(
                    {
                        navSelector     : '.pager',
                        nextSelector    : '.pager a[rel="next"]',
                        itemSelector : '.tile',
                        bufferPx : 80,

                        loading : {
                            selector : "#scroll-loading",
                            img : "/css/asset/sc/round_loader.gif",
                            msgText: "<em>Please wait. Loading more items...</em>",
                            finishedMsg : "<b> You have reached the end of this page </b>",
                            speed: "slow"

                        }

                    },

                    function( newElements ) {
                         // hide new items while they are loading
                        var $newElems = $(newElements).css({ opacity: 0 });
                        $newElems.imagesLoaded(function(){
                            $newElems.css({ opacity: 1 });
                            $container.isotope('appended', $newElems);
                            $("#infscr-loading").fadeOut("slow");
                        });

                    }
                ); 


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
