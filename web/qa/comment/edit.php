<?php

    //sc/qa/comment/edit.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	 
    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    use com\indigloo\sc\auth\Login as Login;
    use com\indigloo\sc\util\PseudoId as PseudoId;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
	$encodedId = Url::getQueryParam("id");
	$commentId = PseudoId::decode($encodedId);

    $commentDao = new com\indigloo\sc\dao\Comment();
    $commentDBRow = $commentDao->getOnId($commentId);

	if(!Login::isOwner($commentDBRow['login_id'])) {
		header("Location: /qa/noowner.php");
		exit ;
	}

	$sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $itemId = PseudoId::encode($commentDBRow['post_id']);
    
    
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> Edit Comment</title>
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
				<div class="span9">
					
					
					<div class="page-header">
						<h2>Edit Comment </h2>
					</div>
					
					<?php FormMessage::render(); ?>
                            
					<div id="form-wrapper">
					<form id="web-form1"  name="web-form1" action="/qa/comment/form/edit.php" enctype="multipart/form-data"  method="POST">

						<div class="error">    </div>
						<table class="form-table">
							 <tr>
								<td>
									<textarea  name="comment" class="w580 h130 required" cols="60" rows="10" ><?php echo $sticky->get('comment',$commentDBRow['description']); ?></textarea>
								</td>
							 </tr>
							 
						</table>
						<div class="form-actions">
							<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button>
							 <a href="/user/dashboard/comments.php"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a> 
						</div>
                                            
                                        
					<input type="hidden" name="comment_id" value="<?php echo $commentDBRow['id'] ; ?>" />
					<input type="hidden" name="item_id" value="<?php echo $itemId;?>" />
					<input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"];?>" />
											
					</form>
					</div> <!-- form wrapper -->
				</div>
			</div>
		</div>
                                   
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
