<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Groups  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?> 

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
           });
        </script>

    </head>

     <body>
        <div class="container mh800">
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
                    <div class="page-header">
                        <h2> Groups </h2>
                    </div>
                    <!-- start cloud -->
                    <?php 
                        if(isset($cloudGroups) && !empty($cloudGroups)) {
                            echo \com\indigloo\sc\html\Group::getCloud($cloudGroups); 
                         }

                    ?>
                    <!-- end cloud -->
                </div> 
            </div>
            
            
        </div>  <!-- container -->
              
       
        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
