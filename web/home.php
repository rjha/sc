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
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <script type="text/javascript" src="/js/sc.js"></script>
        
        
        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.home.addNavGroups();
            });

            var dataObj = new Object();
            dataObj.postId = 1234;
            
        </script>
        
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
                        <?php include(APP_WEB_DIR . '/inc/home-tile.inc'); ?>
                        <?php
                            foreach($this->homeDBRows as $postDBRow) {
                                $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                echo $html ;
                        
                            }

                        ?>
                           
                    </div><!-- tiles -->
                    <hr>
                    <ul class="pager">
                        <li> <a href="<?php echo $nextPageUrl ?>">Next &rarr;</a></li>
                    </ul>

                   <div id="feedback" class="vertical">
                        <a href="/share/feedback.php">
                            <br>
                            F e e d b a c k 
                        </a>
                    </div>  <!-- feedback -->
 
                </div> 
            </div> <!-- row -->
            
            
        </div>  <!-- container -->
              
       
        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
