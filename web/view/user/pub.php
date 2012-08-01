<!DOCTYPE html>
<html>

       <head>
       <title> <?php echo $pageTitle; ?> </title>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <meta name="keywords" content="<?php echo $metaKeywords; ?>">
       <meta name="description" content="<?php echo $metaDescription;  ?>">

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>

        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.toolbar.add();
                webgloo.sc.item.addActions();
            });
        </script>

    </head>

     <body class="">
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

                    <div id="tiles" class="mh600">
                        <!-- user profile tile -->
                        <div class="tile">
                            <?php echo \com\indigloo\sc\html\User::getPublic($userDBRow,$feedDataObj,$total); ?>
                        </div>

                        <?php
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

                    <div class="hr"> </div>
                    <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

                </div>
            </div> <!-- row -->


        </div>  <!-- container -->


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
