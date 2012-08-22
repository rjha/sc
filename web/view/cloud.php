<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Groups  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
            
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
