<!DOCTYPE html>
<html>

    <head>
	    <title> 3mik.com - share and discover shopping items in India  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
      	   
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <script type="text/javascript" src="/3p/json2.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
	    
		
		<script type="text/javascript">
			/* column width = css width + margin */
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.home.addNavGroups();
           });
		</script>
		
    </head>

     <body class="dark-body2">
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
					<div id="tiles">

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
							Y O U R    
							<br />
							<br />
						    F E E D B A C K 	
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
