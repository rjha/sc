<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - <?php echo $pageHeader; ?>  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <script type="text/javascript" src="/3p/json2.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
	    
		
		<script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.home.addNavGroups();
           });
		</script>
		
    </head>

     <body class="">
		<div class="container">
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
				</div> 
				
			</div>
			
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
				</div>
			</div>
			
			
			<div class="row">
				<div class="span12">
					<div class="page-header">
                    <h2> <?php echo $pageHeader; ?> </h2>
					</div>

					<div id="tiles">
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
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
