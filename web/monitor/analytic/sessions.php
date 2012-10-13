<?php
    //sc/monitor/analytic/sessions.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');   

    $analyticDao = new \com\indigloo\sc\dao\Analytic();
    $rows = $analyticDao->currentSessions();

    //print_r($rows); exit ;
?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Sessions </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        
    </head>

    <body>
        <style>
            .name { width:120px;}
        </style>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/monitor/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR.'/monitor/inc/top-unit.inc'); ?>
                </div>
            </div>

             <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>Sessions</h2>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span9">
                    
                    <div class="mt20">
                        <?php
                            echo \com\indigloo\sc\html\Site::getSessionTable($rows);
                        ?>
                    </div>
                </div>
                 
            </div>
        </div> <!-- container -->
        
        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



