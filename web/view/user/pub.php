<!DOCTYPE html>
<html>

       <head>
       <title> <?php echo $pageTitle; ?> </title>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <meta name="keywords" content="<?php echo $metaKeywords; ?>">
       <meta name="description" content="<?php echo $metaDescription;  ?>">
         
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
		</script>
		
    </head>

     <body class="">
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
				<div class="span3">
                    <?php echo \com\indigloo\sc\html\User::getPublicInfo($userDBRow); ?>
                </div> <!-- span3 -->
                <div class="span9">

					<div id="tiles">
						<?php
                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($postDBRows) > 0 ) { 
                                $startId = $postDBRows[0]['id'] ;
                                $endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
                            }	

							foreach($postDBRows as $postDBRow) {
								$html = \com\indigloo\sc\html\Post::getTile($postDBRow);
								echo $html ;
						
							}
						?>
						   
					</div><!-- tiles -->

                    <hr>
                    <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

                    <div id="feedback" class="vertical">
						<a href="/share/feedback.php">
							<br>
                            F e e d b a c k 
						</a>
					</div>	<!-- feedback -->


				</div> 
			</div> <!-- row -->
			
			
		</div>  <!-- container -->
              
       
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>