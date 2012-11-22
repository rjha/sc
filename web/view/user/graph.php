<?php

 
    $gNumRecords = sizeof($graphDBRows);
    $htmlGraph = "" ;

    if($gNumRecords > 0 ) {
       foreach($graphDBRows as $graphDBRow) {
            $htmlGraph .= \com\indigloo\sc\html\SocialGraph::getPubWidget($graphDBRow);
        }

    }else {
        $message = "No records found!" ;
        $htmlGraph = \com\indigloo\sc\html\Site::getNoResult($message);
    }

?>


<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <link rel="stylesheet" type="text/css" href="/css/extra.css" >  

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
            
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <ul class="breadcrumb">
                            <li>
                                <a href="<?php echo $pageBaseUrl; ?>"><?php echo $userDBRow["name"]; ?></a> 
                                <span class="divider">/</span>
                            </li>
                           
                            <li class="active"><?php echo $graphName; ?></li>
                        </ul>

                    </div>
                </div>
            </div>

            <div class="row">

                <div class="span12">
                    <div id="tiles">
                        <?php echo $htmlGraph ;?>
                    </div>
                </div>

            </div>


            <div id="scroll-loading"> </div>

        </div>  <!-- container -->
         
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
             
            $(function(){

                webgloo.sc.item.addActions();
                webgloo.sc.toolbar.add();

            });

        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
