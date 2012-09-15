<?php
    //sc/monitor/groups.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

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
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

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
                <?php include(APP_WEB_DIR . '/monitor/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include('inc/top-unit.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>Groups</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include('inc/menu.inc'); ?>
                </div>
                <div class="span9">
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

                            <input type="hidden" name="q" value="<?php echo Url::current(); ?>" />
                        </form>

                </div>
                <div class="span3"> </div>
            </div>
        </div> <!-- container -->

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



