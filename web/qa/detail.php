<?php

    //sc/share/new.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
	
    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
   	$questionId = Url::getQueryParam("id");
	$questionDao = new \com\indigloo\sc\dao\Question() ;
	$questionDBRow = $questionDao->getOnId($questionId);

?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Share your find, need and knowledge</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		 
		<script type="text/javascript" src="/3p/json2.js" ></script>
		<script type="text/javascript" src="/js/sc.js"></script>
		
	  
        <script type="text/javascript">
       
            $(document).ready(function(){
               
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
				<div class="page-header">
						<h2> 
							Thanks for your post. 
							<a href="/">Home Page</a> 
							&nbsp;/&nbsp;
							<a href="/share/new.php">Add Another</a> 

						</h2>
					</div>
				</div>
			
			
			<div class="row">
				<div class="span4">
					<div> 
						<?php echo \com\indigloo\sc\html\Question::getTile($questionDBRow); ?>
					</div>
				</div> <!-- post summary -->

				<div class="span6">
				
					<p> Add more details to your post. </p>
	
					<?php FormMessage::render(); ?>
					
					<form  id="web-form1"  name="web-form1" action="/qa/form/detail.php" enctype="multipart/form-data"  method="POST">
						<table class="form-table">
							<tr>
								<td>
									<label>Tags</label>
									<input  name="tags" />
								</td>
							</tr>
							
						</table>
						
						
					
						<div class="form-actions"> 
							<button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button> 
							<a href="/"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
						</div>
						        
					</form>
					
		
				</div> <!-- details -->
				<div class="span2">
					<dl>
						<dt> Tags </dt>
						<dd> Tags help others to locate your post quickly. </dd>
					</dl>

				</div> <!-- sidebar -->
				
			</div>
			
		</div> <!-- container -->   
                      
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
