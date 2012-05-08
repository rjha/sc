<?php

    //sc/qa/show.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    
    use com\indigloo\Util as Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
	$postId = Url::getQueryParam("id");

    //Add permanent redirect
    $redirectUrl = "/item/".PseudoId::encode($postId) ;
    header( "HTTP/1.1 301 Moved Permanently" ); 
    header( "Location: ".$redirectUrl );   
    exit ;

    $postDao = new com\indigloo\sc\dao\Post();
    $postDBRow = $postDao->getOnId($postId);

    $imagesJson = $postDBRow['images_json'];
    $images = json_decode($imagesJson);
    
	$linksJson = $postDBRow['links_json'];
	$links = json_decode($linksJson);

    $commentDao = new com\indigloo\sc\dao\Comment();
    $commentDBRows = $commentDao->getOnPostId($postId);

	$loginId = NULL ;

	if(is_null($gSessionLogin)) {
		$login = \com\indigloo\sc\auth\Login::tryLoginInSession();
		if(!is_null($login)) {
			$loginId = $login->id ;
		}
	}

	$loginUrl = "/user/login.php?q=".$_SERVER['REQUEST_URI'];
	
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - <?php echo $postDBRow['title']; ?>  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
       
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
				  interval: 10000
				});
				
            });
        </script>
       
    </head>

     <body>
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
				<div class="span9">
                           
				<?php 
					if(sizeof($images) > 0 ) { include('inc/carousel.inc') ; }
					echo \com\indigloo\sc\html\Post::getDetail($postDBRow) ; 

					if(sizeof($links) > 0 ) {  
						//@todo cleanup kludge in body html
						echo '<div class="p10"/> <ol>' ;
						$tmpl = '<li><a href="{href}" target="_blank">{href} </a></li>';	

						foreach($links as $link) {
							$strLink = str_replace("{href}",$link,$tmpl);
							echo $strLink ;
						}

						echo "</ol> </div>" ;
					}

				?>

				<div class="ml40">
					<span> <a class="btn btn-primary" href="#form-wrapper">Add Comment</a></span>	
				</div>
					
				

				<div class="mt20">
					<?php
						foreach($commentDBRows as $commentDBRow) {
							echo \com\indigloo\sc\html\Comment::getSummary($commentDBRow) ;
						}
					?>
				</div>

                <br/>

                <?php FormMessage::render(); ?>
				<div id="form-wrapper">	
				<form id="web-form1"  name="web-form1" action="/qa/form/comment.php" enctype="multipart/form-data"  method="POST">

					<div class="error">  </div>

					<table class="form-table">
						<tr> 
						<?php if(is_null($loginId)) { ?>
							<td> You need to <a href="<?php echo $loginUrl ?>">login</a></td>
						<?php } ?>
							
						</tr>
						 <tr>
							<td>
								<textarea  name="comment" class="required w580 h130" title="Comment is required" cols="50" rows="4" ><?php echo $sticky->get('comment'); ?></textarea>
							</td>
						 </tr>
						 
						
					</table>
					
					 <div class="form-actions">
						<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Add your comment</span></button>
					</div>

				   <input type="hidden" name="post_id" value="<?php echo $postDBRow['id']; ?>" />
				   <input type="hidden" name="q" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				   
				</form>
				</div> <!-- form-wrapper -->
				
			</div>
		</div>
	</div> <!-- container -->
	
	<div id="ft">
		<?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
	</div>

    </body>
</html>
