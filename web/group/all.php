<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    $groupDao = new \com\indigloo\sc\dao\Group();
    $groups = $groupDao->getLatest(100);
?>


<!DOCTYPE html>
<html>

       <head>
        <title> All groups </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/js/sc.js"></script>
	    
		
		<script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.home.addNavGroups();
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
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/browser.inc'); ?>
				</div>
			</div>
			
			
			<div class="row">
				<div class="span12">
                    <div class="page-header"> Groups </div>
                   
                        <?php
                            //make slices
                            for($i = 0 ; $i <= 10 ; $i++ ) {
                                $offset = $i*10 ;
                                $slice = array_slice($groups,$offset,10);
                                //print these 10 groups
                                $html = \com\indigloo\sc\html\Group::getCard($slice);
                                echo $html ;
                            }

                        ?> 

                </div>

            </div>

        </div> <!-- container -->
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



