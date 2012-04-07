
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
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>

        <script type="text/javascript" src="/js/sc.js"></script>
			
        <script type="text/javascript">			
            $(document).ready(function(){				

				$("#web-form1").validate({
					errorLabelContainer: $("#web-form1 div.error") 
				});

				$('#myCarousel').carousel({
				  interval: 5000
				});

                webgloo.sc.home.addNavGroups();
				
            });
        </script>
       
    </head>

     <body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	</script>

	
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
 	
			
			<div class="row mt20">
				<div class="span8">
                           
				<?php 

                    $imageCount = sizeof($images);
                    if($imageCount > 0 ) { 
                        include($_SERVER['APP_WEB_DIR'].'/qa/inc/carousel.inc') ; 
                    }

                    echo \com\indigloo\sc\html\Post::getDetail($postDBRow) ; 
                    echo \com\indigloo\sc\html\Post::getLinks($links) ; 
                    //gSessionLogin initialized in toolbar
                    echo \com\indigloo\sc\html\Post::getEditBar($gSessionLogin,$postDBRow,$siteDBRow) ; 

                    foreach($commentDBRows as $commentDBRow) {
                        echo \com\indigloo\sc\html\Comment::getSummary($loginId,$commentDBRow) ;
                    }
                ?>

                <br/>

                <?php echo $formErrors; ?>
				<div id="form-wrapper">	
                    <form id="web-form1"  name="web-form1" action="/qa/form/comment.php?q=/item/<?php echo $itemId; ?>" enctype="multipart/form-data"  method="POST">

					<div class="error">  </div>

					<table class="form-table">
						<tr> 
						<?php if(is_null($loginId)) { ?>
							<td> please <a href="<?php echo $loginUrl ?>">login</a> to comment</td>
						<?php } ?>
							
						</tr>
						 <tr>
							<td>
								<textarea  name="comment" class="required w580 h130" title="Comment is required" cols="50" rows="4" ><?php echo $sticky->get('comment'); ?></textarea>
							</td>
						 </tr>
						 
						
					</table>
					
					 <div class="form-actions">
						<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button>
					</div>

				   <input type="hidden" name="post_id" value="<?php echo $postDBRow['id']; ?>" />
				   <input type="hidden" name="q" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				   
				</form>
				</div> <!-- form-wrapper -->
				
			</div>
			<div class="span4">
                    <h3> Also on 3mik </h3>
					<?php
						foreach($xrows as $xrow) {
							echo \com\indigloo\sc\html\Post::getSimpleTile($xrow) ;
						}
					?>
			</div>
		</div>
	</div> <!-- container -->
	
	<div id="ft">
		<?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
	</div>

    </body>
</html>
