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

       
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <link rel="stylesheet" type="text/css" href="/3p/fancybox/jquery.fancybox-1.3.4.css"></script>
        <script type="text/javascript" src="/3p/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/js/sc.js"></script>
			
        <script type="text/javascript">			
            $(document).ready(function(){				

				$("#web-form1").validate({
					errorLabelContainer: $("#web-form1 div.error") 
				});

                webgloo.sc.home.addNavGroups();
                webgloo.sc.home.addSmallTiles();
                webgloo.sc.item.addActions();
                
                $("a.gallery").fancybox();
				
            });
        </script>
       
    </head>

     <body>
	
		<div class="container mh800">
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
				</div> 
				
			</div>
			
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/browser.inc'); ?>
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
                        echo \com\indigloo\sc\html\Post::getToolbar($itemId,$loginId,$postDBRow['login_id']) ; 
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
                        //gSessionLogin initialized in toolbar
                        echo \com\indigloo\sc\html\Post::getEditBar($gSessionLogin,$postDBRow) ; 
                        echo \com\indigloo\sc\html\Post::getDetail($postDBRow) ; 
                        foreach($commentDBRows as $commentDBRow) {
                            echo \com\indigloo\sc\html\Comment::getSummary($loginId,$commentDBRow) ;
                        }
                        include($_SERVER['APP_WEB_DIR'].'/qa/inc/comment.inc') ; 
                    ?>

              </div>
            </div> <!-- row -->

       	</div> <!-- container -->
	
	<div id="ft">
		<?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
	</div>

    </body>
</html>
