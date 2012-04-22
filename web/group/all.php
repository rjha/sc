<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $total = $groupDao->getTotalCount();

    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    $pageSize =	100;
    $paginator = new Pagination($qparams,$total,$pageSize);	
    $groups = $groupDao->getPaged($paginator);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($groups) > 0 ) {
        $startId = $groups[0]['id'] ;
        $endId =   $groups[sizeof($groups)-1]['id'] ;
    }

    $pageBaseUrl = "/group/all.php" ;

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
                            $numCards = ceil((sizeof($groups)/10.0)) ;

                            for($i = 0 ; $i < $numCards ; $i++ ) {
                                $offset = $i*10 ;
                                $slice = array_slice($groups,$offset,10);
                                //print these 10 groups
                                $html = \com\indigloo\sc\html\Group::getCard($slice);
                                echo $html ;
                            }

                        ?> 

                </div>

            </div>
            <hr>
            <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

        </div> <!-- container -->
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



