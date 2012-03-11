<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

	use \com\indigloo\Url as Url ;
	use \com\indigloo\Logger as Logger ;
	use \com\indigloo\sc\auth\Login as Login ;

    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\form\Message as FormMessage;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

	$qUrl = Url::tryQueryParam('q');
	$qUrl = empty($qUrl) ? '/user/dashboard.php' : $qUrl;

	$questionId = Url::getQueryParam("id");
	$questionDao = new \com\indigloo\sc\dao\Question();
	$questionDBRow = $questionDao->getOnId($questionId);

	if(!Login::isOwner($questionDBRow['login_id'])) {
		header("Location: /qa/noowner.php");
        exit ;
	}

?>  

<!DOCTYPE html> 
<html>

       <head>
        <title>3mik.com - Delete a post</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/css/sc.css">
		
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		 
    </head>

    <body>
		<div class="container mh800">
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
						<h2> Delete Post</h2>
					</div>
					<div class="alert">
					  <a class="close" data-dismiss="alert">×</a>
					  <strong>Warning!</strong> There is no way to recover a deleted post.
					  Please make sure that you really want to delete this post.
					</div>
					
					<?php FormMessage::render(); ?>
					<?php echo \com\indigloo\sc\html\Question::getWidget(NULL,$questionDBRow); ?>

					<form id="web-form1"  name="web-form1" action="/qa/form/delete.php" method="POST">
						<div>
							<button class="btn btn-danger" type="submit" name="delete" value="Delete" onclick="this.setAttribute('value','Delete');">Delete</button>
							<a href="<?php echo $qUrl; ?>"><button class="btn" type="button">Cancel</a></button></a>
						</div>
						<input type="hidden" name="q" value="<?php echo $qUrl; ?>" /> 
						<input type="hidden" name="question_id" value="<?php echo $questionId; ?>" />
					</form>
		
				</div>
			</div>
		</div> <!-- container -->

        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
