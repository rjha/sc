<!DOCTYPE html>
<html>

       <head>
        <title> <?php echo $pageTitle; ?>  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>
	    
		
		<script type="text/javascript">
			/* column width = css width + margin */
			$(document).ready(function(){
				var $container = $('#tiles');
				$container.imagesLoaded(function(){
					$container.masonry({
						itemSelector : '.tile'
						
					});
				});

                //show options on hover
                $('.tile .options').hide();
                $('.tile').mouseenter(function() { $(this).find('.options').toggle(); });
                $('.tile').mouseleave(function() { $(this).find('.options').toggle(); }); 

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
                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($questionDBRows) > 0 ) { 
                                $startId = $questionDBRows[0]['id'] ;
                                $endId =   $questionDBRows[sizeof($questionDBRows)-1]['id'] ;
                            }	

							foreach($questionDBRows as $questionDBRow) {
								$html = \com\indigloo\sc\html\Question::getTile($questionDBRow);
								echo $html ;
						
							}
						?>
						   
					</div><!-- tiles -->
                    <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

				</div> 
			</div>
			
			
		</div>  <!-- container -->
              
       
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
