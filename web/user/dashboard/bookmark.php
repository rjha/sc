<?php
    //sc/user/dashboard/bookmark.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    
    use \com\indigloo\sc\ui\Constants as UIConstants ;
     
    //$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $code = Url::tryQueryParam("code");
    $code = is_null($code) ? 1 : $code ;
    
    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }
    
    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);
    
    if (empty($userDBRow)) {
        trigger_error("No user record found for given login_id", E_USER_ERROR);
    }
    
    $tileOptions = ~UIConstants::TILE_ALL ;
    $pageTitle = "%s %s on 3mik" ;
    
    // LIKE - 1 
    // SAVE - 2 
    switch($code){
        case 1: 
            $tileOptions = UIConstants::TILE_SAVE ;
            $pageTitle = sprintf($pageTitle,$userDBRow['user_name'], "'s likes") ; 
            break;
        case 2:
            $tileOptions = UIConstants::TILE_REMOVE ;
            $pageTitle = sprintf($pageTitle,$userDBRow['user_name'], "'s favorites") ; 
            break;
        default:
            break ;
    }

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
    $postDBRows = $bookmarkDao->getOnLoginId($loginId,$code);

            
?>
<!DOCTYPE html>
<html>

       <head>
       <title> <?php echo $pageTitle; ?> </title>
       <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <script type="text/javascript" src="/js/sc.js"></script>
	    
		
		<script type="text/javascript">
			/* column width = css width + margin */
			$(document).ready(function(){
                webgloo.sc.home.addTiles();
			});
		</script>
		
    </head>

     <body class="">
		<div class="container mh800">
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
				</div> 
				
			</div>
			
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
                    <div class="page-header">
                        <h2> <?php echo $pageTitle; ?> </h2>
					</div>
				</div>
			</div>
			
			
			<div class="row">
				<div class="span9">
					
					<div id="tiles">
						<?php
                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($postDBRows) > 0 ) { 
                                $startId = $postDBRows[0]['id'] ;
                                $endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
                            }	

							foreach($postDBRows as $postDBRow) {
								$html = \com\indigloo\sc\html\Post::getTile($postDBRow,$tileOptions);
								echo $html ;
						
							}
						?>
						   
					</div><!-- tiles -->
                    <hr>
                    <?php //$paginator->render($pageBaseUrl,$startId,$endId);  ?>

				</div> 
                <div class="span3">
                     <?php include('inc/menu.inc'); ?>
                </div>
			</div>
			
		</div>  <!-- container -->
              
       
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

