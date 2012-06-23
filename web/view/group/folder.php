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

        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?> 
        
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
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div> 
                
            </div>
            
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                    <?php include(APP_WEB_DIR . '/inc/browser.inc'); ?>
                </div>
            </div>
            
            
            <div class="row">
                <div class="span12">
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
        </div> <!-- container -->
        <div class="hr"> </div>
        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>
        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
         