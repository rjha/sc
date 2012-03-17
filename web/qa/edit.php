<?php

    //sc/qa/edit.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use com\indigloo\Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;

    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\SelectBox as SelectBox;
    use com\indigloo\ui\form\Message as FormMessage;
	use \com\indigloo\sc\auth\Login as Login ;
	use \com\indigloo\sc\util\PseudoId as PseudoId ;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

	$itemId = Url::getQueryParam("id");
	$postId = PseudoId::decode($itemId);

    $postDao = new \com\indigloo\sc\dao\Post();
    $postDBRow = $postDao->getOnId($postId);
	

	if(!Login::isOwner($postDBRow['login_id'])) {
		header("Location: /qa/noowner.php");
		exit(1);
	}

    $loginId = Login::getLoginIdInSession() ;

    $imagesJson = $postDBRow['images_json'];
    $strImagesJson = $sticky->get('images_json',$postDBRow['images_json']) ;
    $strLinksJson = $sticky->get('links_json',$postDBRow['links_json']) ;

    $strImagesJson = empty($strImagesJson) ? '[]' : $strImagesJson ;
    $strLinksJson = empty($strLinksJson) ? '[]' : $strLinksJson ;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $group_names = $groupDao->slugToName($postDBRow['group_slug']);
    $hasGroups = (($groupDao->getCountOnLoginId($loginId)) > 0) ? true : false;


?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Share your find, need and knowledge</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		<link rel="stylesheet" type="text/css" href="/3p/ful/valums/fileuploader.css">
		<link rel="stylesheet" type="text/css" href="/3p/fancybox/jquery.fancybox-1.3.4.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		 
		<script type="text/javascript" src="/3p/ful/valums/fileuploader.js" ></script>
        <script type="text/javascript" src="/3p/fancybox/jquery.fancybox-1.3.4.pack.js"></script>

		<script type="text/javascript" src="/3p/json2.js"></script>
		<script type="text/javascript" src="/js/sc.js"></script>
		
	  
        <script type="text/javascript">
       
            $(document).ready(function(){
               
				$("#web-form1").validate({
					   errorLabelContainer: $("#web-form1 div.error") 
				});

                              					
				webgloo.media.init(["link","image"]);
				webgloo.media.attachEvents();
				webgloo.sc.groups.addCloudBox();
				  
				var uploader = new qq.FileUploader({
					element: document.getElementById('image-uploader'),
					action: '/upload/image.php',
					debug: true,
					onComplete: function(id, fileName, responseJSON) {
						 webgloo.media.addImage(responseJSON.mediaVO);
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
				<div class="span9">
					
					
					<div class="page-header">
						<h2> Edit </h2>
					</div>
					
					<?php FormMessage::render(); ?>
					
					<form  id="web-form1"  name="web-form1" action="/qa/form/edit.php" enctype="multipart/form-data"  method="POST">
						<div class="row">
							<div class="span9"><div id="image-uploader"> </div></div>
						</div>
                        <table class="form-table">
							
                            <tr>
                                <td>
									<label>Details</label>
									<textarea  name="description" class="required h130 w500" cols="50" rows="4" ><?php echo $sticky->get('description',$postDBRow['description']); ?></textarea>
								</td>
							</tr>
                            <tr>
                                <td> <label>Tags (separate by comma)
                                    <?php if($hasGroups) { ?>
                                        &nbsp;|&nbsp;<a class="fancy-box" href="/user/group/cloud.php">show my tags&rarr;</a> </label>
                                    <?php } ?>
                                    <input type="text" name="group_names" value="<?php echo $sticky->get('group_names',$group_names); ?>" />

                            </tr>

							<tr>
								<td>
									<label>Link </label>
                                    <input id="link-box" name="link" value="<?php echo $sticky->get('link'); ?>" />
									<button id="add-link" type="button" class="btn" value="Add"><i class="icon-plus-sign"> </i>&nbsp;Add</button> 
								</td>
							</tr>
                            <tr>
                                <td>
	
                                    <div class="form-actions"> 
                                        <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button> 
                                        <a href="/"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

								</td>
							</tr>
 
						</table>

                        <div id="link-data"> </div>
                        <div id="image-data"> </div>
						
						<input type="hidden" name="links_json" value='<?php echo $strLinksJson ; ?>' />
						<input type="hidden" name="images_json" value='<?php echo $strImagesJson ; ?>' />
						<input type="hidden" name="post_id" value="<?php echo $postDBRow['id'];?>" />	
						<input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"];?>" />	
						        
					</form>
					
									
				   
				</div> <!-- span9 -->
				
				<div class="span3">
					 <?php include($_SERVER['APP_WEB_DIR'] .'/qa/sidebar/edit.inc'); ?>
				</div>

			</div>
			
		</div> <!-- container -->   
                      
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
