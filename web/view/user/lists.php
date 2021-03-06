
<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
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
                                <a href="<?php echo $pageBaseUrl; ?>">&larr; Back</a> 
                                <span class="divider">/</span>
                            </li>
                            <li>
                                <a href="<?php echo $pageBaseUrl; ?>"><?php echo $userDBRow["name"]; ?></a> 
                                <span class="divider">/</span>
                            </li>
                           
                            <li class="active"><?php echo $pageTitle; ?></li>
                        </ul>

                    </div>
                </div>
            </div>

            <div class="row">

                <div class="span12">
                    <div id="widgets">
                         <?php

                            $startId = NULL;
                            $endId = NULL ;
                            $gNumRecords = sizeof($listDBRows);
                            

                            if($gNumRecords > 0 ) {
                                $startId = $listDBRows[0]['id'] ;
                                $endId =   $listDBRows[$gNumRecords-1]['id'] ;
                                foreach($listDBRows as $listDBRow) {
                                    echo \com\indigloo\sc\html\Lists::getPubWidget($listDBRow);
                                }
                            }else {
                                $message = "No lists found!" ;
                                $htmlItems = \com\indigloo\sc\html\Site::getNoResult($message,$options);
                            }

                        ?>
                    </div>
                </div>

            </div>

        </div>  <!-- container -->

        <?php $paginator->render($pageBaseUrl,$startId,$endId,$gNumRecords);  ?>
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            
            $(function(){

                //Add item toolbar actions
                webgloo.sc.item.addActions();
                webgloo.sc.toolbar.add();

            });

        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
