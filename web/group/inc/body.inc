
<!DOCTYPE html>
<html>

       <head>
       <title> <?php echo $title; ?> </title>
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
		<div class="container">
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
                    <div class="stack">
                    <h1> <?php echo $title; ?> </h1>
                        <br>
                        <?php if($hasNavigation) { ?> 
                        <div class="well">
                            <ul class="nav nav-list">
                               <li class="nav-header">Browse Groups</li>
                               <li><a href="/group/all.php">Latest Groups</a></li>
                               <li><a href="/group/alpha.php">Alphabetical</a></li>
                               <li><a href="/group/random.php">Random</a></li>
                            </ul>
                        </div> 
                        <?php } ?> 
                       </div> <!-- first stack -->


                   
                        <?php
                            //make slices
                            $numCards = ceil((sizeof($groups)/10.0)) ;

                            for($i = 0 ; $i < $numCards ; $i++ ) {
                                $offset = $i*10 ;
                                $slice = array_slice($groups,$offset,10);
                                //print these 10 groups
                                $style = ($i % 3 == 0 ) ? 2 : 1 ;
                                $html = \com\indigloo\sc\html\Group::getCard($slice,$style);
                                echo $html ;
                            }

                        ?> 

                </div>

            </div>
         
