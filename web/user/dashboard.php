<?php

    //sc/user/dashboard.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');
		
    use com\indigloo\Util as Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Configuration as Config;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\ui\Tabs as Tabs ;
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

	$gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id ;

	if(is_null($loginId)) {
		trigger_error("Error : NULL login_id on user dashboard",E_USER_ERROR);
	}

    //show dashboard to owners only
	
    $userDao = new \com\indigloo\sc\dao\User() ;
	$userDBRow = $userDao->getOnLoginId($loginId);

	if(empty($userDBRow)) {
		trigger_error("No user record found for given login_id" , E_USER_ERROR);
	}

    $tabOptions = array("post"=>"Posts","comment"=>"Comments");
    $tabData = Tabs::render($tabOptions,'post');
    $strTabUI = $tabData["buffer"];
    $activeTab = $tabData["active"];

	$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

		<link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/jquery.xeyes.1.0.min.js"></script>
		<script>
			$(document).ready(function(){
				//xeyes
				$('.iris').xeyes({
					padding: '12px',
					position: 'topRight'
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
				<div class="span12">
					<div class="page-header">
						<h2> <?php echo $userDBRow["name"]; ?> </h2>
					</div>
					<?php echo \com\indigloo\sc\html\User::getProfile($gSessionLogin,$userDBRow) ; ?>
				</div>
			</div>

			<div class="row">
				<div class="span9">
					<div class="mt20">
                        <?php echo $strTabUI; ?>
                        <?php include($_SERVER['APP_WEB_DIR']."/user/inc/$activeTab.php"); ?>
					</div> <!-- wrapper -->

				</div>
			</div>
		</div> <!-- container -->
		
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
