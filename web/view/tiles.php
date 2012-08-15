<!DOCTYPE html>
<html>

       <head>
        <title> <?php echo $pageTitle; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

     <body class="dark-body">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <div class="page-header">
                    <h2> <?php echo $pageHeader; ?> </h2>
                    </div>

                    <div id="tiles" class="mh600">
                        <?php
                            if(sizeof($postDBRows) > 0 ){
                                foreach($postDBRows as $postDBRow) {
                                    $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                    echo $html ;

                                }
                            } else {
                                $message = "No results found " ;
                                echo \com\indigloo\sc\html\NoResult::get($message);
                            }
                        ?>

                    </div><!-- tiles -->

                </div>
            </div>


        </div>  <!-- container -->



        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.toolbar.add();
           });
        </script>

         <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
