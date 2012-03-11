<?php

    //sc/share/feedback.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
		
    use com\indigloo\Util;
	use \com\indigloo\sc\auth\Login as Login ;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

	$loginId = NULL ;
	$userName = '';

	$gSessionLogin = Login::tryLoginInSession();
	if(!is_null($gSessionLogin)) {
		$loginId = $gSessionLogin->id ;
		$userName = $gSessionLogin->name ;
	}
    
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - share your feedback</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		 
        <script type="text/javascript">
       
            $(document).ready(function(){
               
				$("#web-form1").validate({
					   errorLabelContainer: $("#web-form1 div.error") 
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
				<div class="span8">
					
					
					<div class="page-header">
						<h2> We appreciate your feedback </h2>
					</div>
					
					<?php FormMessage::render(); ?>
					
					<form  id="web-form1"  name="web-form1" action="/share/form/feedback.php" enctype="multipart/form-data"  method="POST">
						<table class="form-table">
							<tr>
								<td>
									<label>Feedback</label>
									<textarea  name="feedback" style="width:600px" class="required h130 " cols="50" rows="4" ><?php echo $sticky->get('feedback'); ?></textarea>
								</td>
							</tr>
							
						</table>
						
						
					
						<div class="form-actions"> 
							<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Send your feedback</span></button> 
							<a href="/"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
						</div>
						        
					</form>
					
									
				   
				</div> <!-- content -->
				
				<div class="span4">
					<!-- sidebar -->
				</div>
			
			</div>
			
		</div> <!-- container -->   
                      
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
