<?php
    //sc/monitor/groups.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\sc\Constants as AppConstants;
    use \com\indigloo\ui\form\Message as FormMessage;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $dbslug = $groupDao->getFeatureSlug();


?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Featured Groups </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            $(document).ready(function(){
                 webgloo.sc.admin.addSlugPanelEvents();
            });

        </script>

    </head>

    <body>
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
                        <h2>Featured Groups</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>

                <div class="span8">
                    <?php FormMessage::render(); ?>
                        <div id="form-wrapper">
                            <form name="web-form1" action="/monitor/form/group/featured.php" method="POST">
                                
                                <?php echo \com\indigloo\sc\html\Site::renderAddBox(); ?>
                                <?php echo \com\indigloo\sc\html\Site::renderSlugPanel($dbslug); ?>

                                <input type="hidden" name="q" value="<?php echo Url::current(); ?>" />
                                <div class="mt20 p10">
                                    <button class="btn gBtnUp b flickr-color" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button>
                                 
                                </div>
                            </form>
                        </div>
                </div>
                
            </div>
        </div> <!-- container -->

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



