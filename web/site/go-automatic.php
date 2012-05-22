<!DOCTYPE html>
<html>

       <head>
        <title> <?php echo $title; ?> </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $goUrl; ?>'; }, 5000); 
        </script>
         
    </head>

    <body>
        <div class="container mh800">
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
                <div class="span9">
                    
                    
                    <div class="page-header">
                        <h2> <?php echo $header; ?> </h2>
                    </div>

                    <div class="p20">
                        <img src="/css/images/ajax_loader.gif" alt="ajax loader" />
                    </div>      
                    
                    <div class="well">
                        <p class="help-text">
                            <a class="btn btn-large" href="<?php echo $goUrl; ?>"><?php echo $goText; ?> </a>
                        </p>   
                    </div>

                </div>
            </div>
        </div> <!-- container -->

        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
