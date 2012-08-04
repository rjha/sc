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
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.toolbar.add();
           });
        </script>

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
                        <div class="tile">
                            <div class="well">
                                <ul class="nav nav-list">
                                   <li class="nav-header">More Groups</li>
                                   <li><a href="/group/all.php">Latest Groups</a></li>
                                   <li><a href="/group/alpha.php">Alphabetical</a></li>
                                   <li><a href="/group/random.php">Random</a></li>
                                </ul>
                            </div>
                        </div> <!-- navigation tile -->


                        <?php
                            foreach($postDBRows as $postDBRow) {
                                $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                echo $html ;

                            }
                        ?>

                    </div><!-- tiles -->
                </div>
            </div>


        </div>  <!-- container -->


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
