
<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - <?php echo $pageTitle; ?>  </title>
         
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="3mik, share, discover, india, cool shopping items, shopping bookmarking">
        <meta name="description" content="<?php echo $pageMetaDescription;  ?>">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

       
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>

        <script type="text/javascript" src="/3p/json2.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
			
        <script type="text/javascript">			
            $(document).ready(function(){				

				$("#web-form1").validate({
					errorLabelContainer: $("#web-form1 div.error") 
				});

				$('#myCarousel').carousel({
				  interval: 5000
				});
				
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
                        <h2> <?php echo $pageTitle; ?>  </h2>
                    </div>
				</div>
			</div>
 	
			
			<div class="row">
				<div class="span8">
                           
				<?php 
					if(sizeof($images) > 0 ) { include($_SERVER['APP_WEB_DIR'].'/qa/inc/carousel.inc') ; }
					echo \com\indigloo\sc\html\Question::getDetail($questionDBRow) ; 

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

					echo \com\indigloo\sc\html\Question::getEditBar($gSessionLogin,$questionDBRow) ; 

				?>

				<div class="mt20">
					<?php
						foreach($answerDBRows as $answerDBRow) {
							echo \com\indigloo\sc\html\Answer::getSummary($loginId,$answerDBRow) ;
						}
					?>
				</div>

                <br/>

                <?php echo $formErrors; ?>
				<div id="form-wrapper">	
				<form id="web-form1"  name="web-form1" action="/qa/form/answer.php" enctype="multipart/form-data"  method="POST">

					<div class="error">  </div>

					<table class="form-table">
						<tr> 
						<?php if(is_null($loginId)) { ?>
							<td> You need to <a href="<?php echo $loginUrl ?>">login</a></td>
						<?php } ?>
							
						</tr>
						 <tr>
							<td>
								<textarea  name="answer" class="required w580 h130" title="Answer is required" cols="50" rows="4" ><?php echo $sticky->get('answer'); ?></textarea>
							</td>
						 </tr>
						 
						
					</table>
					
					 <div class="form-actions">
						<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button>
					</div>

				   <input type="hidden" name="question_id" value="<?php echo $questionDBRow['id']; ?>" />
				   <input type="hidden" name="q" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				   
				</form>
				</div> <!-- form-wrapper -->
				
			</div>
			<div class="span4">
               
				<div class="p10">	
                    <div class="fb-like" data-href="<?php echo $pageUrl;?>" data-send="true" data-layout="button_count" data-width="220" data-show-faces="false"></div>
				</div>
				<div class="p10">
					<a href="https://twitter.com/share" class="twitter-share-button" data-via="3mikindia" data-count="none">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                </div>	
                <div class="block">
                    <h3> More from 3mik </h3>
                </div>
					<?php
						foreach($tileDBRows as $tileDBRow) {
							echo \com\indigloo\sc\html\Question::getSimpleTile($tileDBRow) ;
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
