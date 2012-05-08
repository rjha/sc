<?php
    //sc/monitor/groups.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use com\indigloo\ui\form\Message as FormMessage;
    
    $groupDao = new \com\indigloo\sc\dao\Group();
    $slug = $groupDao->getFeatureSlug();
    

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Featured Groups </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
         
        <script>
            $(document).ready(function(){
                 webgloo.sc.groups.addPanelEvents();
            });
            
        </script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR'] . '/monitor/inc/toolbar.inc'); ?>
                </div> 

            </div>

            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR']. '/monitor/inc/banner.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    <div class="page-header"> <h2>Groups</h2> </div>
                    <?php FormMessage::render(); ?>
                        <form name="web-form1" action="/monitor/form/group/featured.php" method="POST">
                            <div class="row">
                                <div class="span12">
                                    <?php echo \com\indigloo\sc\html\GroupPanel::render($slug); ?> 
                                </div>
                            </div>
                            <div class="form-actions"> 
                                <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button> 
                                <a href="/monitor/posts.php"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                            </div>

                            <input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"]; ?>" />
                        </form>
    
                </div>
                <div class="span3">
                     <?php include($_SERVER['APP_WEB_DIR'].'/monitor/inc/menu.inc'); ?>
                </div>
            </div>
        </div> <!-- container -->
        
        <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


