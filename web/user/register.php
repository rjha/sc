<?php

    //sc/user/register.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	//@see http://www.google.com/recaptcha
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/recaptchalib.php');
    
    use com\indigloo\Util;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
   
?>  

<!DOCTYPE html>
<html>

       <head><title> User registration page  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>


		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                //form validator
                //http://docs.jquery.com/Plugins/Validation/Methods/equalTo
                //new jquery validate plugin can accept rules
                
                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error"),
                    rules: {
                        password: "required",
                        password_again: {
                            equalTo: "#password"
                        },
                        email : {
                            required: true ,
                            email : true
                        }
                     }
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
                        <h2> Register </h2>
                    </div>
					<p class="help-text">
					   Please provide details below to register. Password should be atleast 8 characters.

					</p>
					
					<?php FormMessage::render(); ?>
                                
					<form id="web-form1"  name="web-form1" action="/user/form/register.php" enctype="multipart/form-data"  method="POST">

						<div class="error">    </div>

						<table class="form-table">

							 <tr>
								<td class="field">First Name<span class="red-label">*</span></td>
								<td>
									<input type="text" name="first_name" maxlength="32" class="required" title="&nbsp;First Name is required" value="<?php echo $sticky->get('first_name'); ?>"/>
								</td>
							 </tr>
							  <tr>
								<td class="field">Last Name<span class="red-label">*</span></td>
								<td>
									<input type="text" name="last_name" maxlength="32" class="required" title="&nbsp;Last Name is required" value="<?php echo $sticky->get('last_name'); ?>"/>
								</td>
							 </tr>

							  <tr>
								<td class="field"> Email<span class="red-label">*</span></td>
								<td>
									<input type="text" id="email" name="email" maxlength="64" class="required mail" title="&nbsp;Enter a valid email" value="<?php echo $sticky->get('email'); ?>"/>
								</td>
							</tr>
			
							   
							 <tr>
								<td class="field">Password<span class="red-label">*</span> &nbsp; </td>
								<td> <input id="password" type="password" name="password" maxlength="32" class="required" minlength="8" title="password should be atleast 8 chars!" value="" /></td>
							</tr>

							<tr>
								<td class="field">Confirm Password <span class="red-label">*</span> &nbsp;</td>
								<td> <input id="password_again" type="password" name="password_again" maxlength="32" class="required" minlength="8"  title="passwords do not match" value="" /></td>
							</tr>

							
						</table>
						
						<div class="ml20">
							<p> Type the two words in the image below </p>
							<?php
								$publickey = "6Lc3p80SAAAAAJvGjs1RyMl8zHBRtg1sf1nRwnJn"; 
								echo recaptcha_get_html($publickey);
						   ?>
									
						</div>
						
						<div class="form-actions">
							<button class="btn btn-primary" type="submit" name="register" value="Register" onclick="this.setAttribute('value','Register');" ><span>Register</span></button>
							 <a href="/">
								<button class="btn" type="button" name="cancel"><span>Cancel</span></button>
							</a>
							
						</div>

					</form>
				</div>
				
			<div class="span4">
				<?php include('sidebar/register.inc'); ?>
            </div> 
        </div>
	</div> <!-- container -->
		
	<div id="ft">
		<?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
	</div>

    </body>
</html>
