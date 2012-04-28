<?php

    //sc/site/wf/password/mail.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	
    use com\indigloo\Util;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
   
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Share your find, need and knowledge</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/js/sc.js"></script>
		
	  
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
				<div class="span9">
					
					
					<div class="page-header">
						<h2> Forgot your password? </h2>
					</div>
					
					<?php FormMessage::render(); ?>
					
					<form  id="web-form1"  name="web-form1" action="/site/wf/password/form/mail.php" enctype="multipart/form-data"  method="POST">
						<div class="row">
							<div class="span9">
								<div id="image-uploader"> </div>
							</div>
						</div> <!-- top row -->
						<table class="form-table">
                            <tr>
                                <td> <label>Your email</label>
                                <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                            </tr>
 
							<tr>
								<td>
                                    <p>To reset your password, enter your email.  A mail will be sent along with the instructions to reset your password.
								</td>
							</tr>

							<tr>
								<td>
                                  	<div class="form-actions"> 
                                        <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button> 
                                        <a href="/"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>
  
                                </td>
							</tr>
							
						</table>
					
						<input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"]; ?>" />
												
					</form>
									
				   
				</div> <!-- span9 -->
				
				<div class="span3">
                    <!-- sidebar -->
                
				</div>
			
			</div> <!-- row -->
			
		</div> <!-- container -->   
                      
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
