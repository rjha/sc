<?php

    //sc/user/profile/edit.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	 
    use com\indigloo\Util;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
	use \com\indigloo\sc\auth\Login as Login ;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
	
	$gSessionLogin = Login::getLoginInSession();
	$loginId = $gSessionLogin->id ;

    $userDao = new \com\indigloo\sc\dao\User() ;
	$userDBRow = $userDao->getonLoginId($loginId);
   
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> Edit Profile - <?php echo $userDBRow['first_name']; ?>  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){
                $("#web-form1").validate({errorLabelContainer: $("#web-form1 div.error")});

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
						<h2> Edit Profile - <?php echo $userDBRow['first_name']; ?> </h2>
					</div>
								
					<p class="help-text">
					   Please update the details and click on Save.

					</p>
					
					<?php FormMessage::render(); ?>
			
						<form id="web-form1"  name="web-form1" action="/user/profile/form/edit.php" enctype="multipart/form-data"  method="POST">

							<div class="error">    </div>

							<table class="form-table">

								 <tr>
									<td class="field">First Name<span class="red-label">*</span></td>
									<td>
										<input type="text" name="first_name" maxlength="32" class="required" title="&nbsp;First Name is required" value="<?php echo $sticky->get('first_name',$userDBRow['first_name']); ?>"/>
									</td>
								 </tr>
								  <tr>
									<td class="field">Last Name<span class="red-label">*</span></td>
									<td>
										<input type="text" name="last_name" maxlength="32" class="required" title="&nbsp;Last Name is required" value="<?php echo $sticky->get('last_name',$userDBRow['last_name']); ?>"/>
									</td>
								 </tr>
								
							</table>

							<div class="form-actions">
								<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button>
								 <a href="/">
									<button class="btn" type="button" name="cancel"><span>Cancel</span></button>
								</a>
								
							</div>
							
							<div style="clear: both;"></div>

						</form>
				</div>
			</div>
		</div> <!-- container -->
		
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
