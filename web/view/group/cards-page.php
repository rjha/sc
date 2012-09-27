<!DOCTYPE html>
<html>

       <head>
       <title> <?php echo $title; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

     <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        
        <div class="container">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span12">
                    <h2> <?php echo $title ; ?> </h2>
                </div>
            </div>
            <div class="row">
                <div class="span12 mt20">
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
        <div class="mt20"> </div>
        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
           });
        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>


    </body>
</html>