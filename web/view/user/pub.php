<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>

    </head>

     <body class="dark-body">
        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>
            </div>

            <div class="row">

                <div class="span12">

                    <div id="tiles" class="mh600">

                        <?php

                            //include user information tile on page#1 only!
                            if($gpage == 1 ) {
                                echo '<div class="tile">' ;
                                echo \com\indigloo\sc\html\User::getPublic($userDBRow,$feedDataObj,$total);
                                echo '</div>' ;
                            }

                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($postDBRows) > 0 ) {
                                $startId = $postDBRows[0]['id'] ;
                                $endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
                                foreach($postDBRows as $postDBRow) {
                                    $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                    echo $html ;

                                }
                            }else {
                                $message = "No Posts" ;
                                echo \com\indigloo\sc\html\NoResult::getTile($message);

                            }

                        ?>

                    </div><!-- tiles -->

                    <hr>


                </div>
            </div> <!-- row -->


            <div id="scroll-loading"> </div>

        </div>  <!-- container -->

        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

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
                        masonry: { columnWidth :235 } ,
                        onLayout : function( $elems, instance ) {
                            add_tile_options();
                        }                   
                    });

                });


                $container.infinitescroll(
                    {
                        navSelector  	: '.pager',
                        nextSelector 	: '.pager a[rel="next"]',
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
