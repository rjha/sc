<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

	$qUrl = "/" ;
   
    
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> Not allowed to do this action</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script>
			window.setTimeout(function() {window.location.href = '<?php echo $qUrl; ?>'; }, 5000); 
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
				</div>
			</div>
			
			
			<div class="row">
				<div class="span9">
					
					
					<div class="page-header">
						<h2> You do not have the required permissions. Redirecting... </h2>
					</div>

				    <div class="p20">
						<img src="/css/images/ajax_loader.gif" alt="ajax loader" />
					</div>		
					
					<div class="well">
						<p class="help-text">
						   <a class="btn btn-primary" href="/">Home Page</a>
						</p>   
					</div>

				</div>
			</div>
		</div> <!-- container -->

        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
