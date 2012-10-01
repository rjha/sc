<?php
    //sc/user/dashboard/mails.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\Constants as AppConstants ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user exists with this login_id", E_USER_ERROR);
    }

    $fUrl = Url::current();
    $preferenceDao = new \com\indigloo\sc\dao\Preference();
    $pData = $preferenceDao->get($loginId);

    $checked = array();
    $checked["follow"] = ($pData->follow) ? "checked" : "" ;
    $checked["comment"] = ($pData->comment) ? "checked" : "" ;
    $checked["bookmark"] = ($pData->bookmark) ? "checked" : "" ;

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>


    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
            <div class="row">
                <div class="span9 mh600">
                    <div class="page-header">
                        <div class="faded-text"> Mail settings </div>
                    </div>

                    <?php FormMessage::render(); ?>

                    <div id="form-wrapper">
                        <form id="web-form1"  name="web-form1" action="/user/form/mails-preference.php" method="POST">
                            <table class="table table-striped">

                                <tbody>
                                    <tr>
                                        <td><input type="checkbox" name="p[follow]" value="true" <?php echo $checked["follow"]; ?> /></td>
                                        <td> When people start following me</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="p[comment]" value="true" <?php echo $checked["comment"]; ?>/></td>
                                        <td> When people comment on my post</td>
                                     </tr>
                                     <tr>
                                        <td><input type="checkbox" name="p[bookmark]" value="true" <?php echo $checked["bookmark"]; ?>/></td>
                                        <td> When people like or save my post </td>
                                    </tr>
                                     <tr>
                                        <td>&nbsp;</td>
                                        <td> &nbsp; </td>
                                    </tr>
                                </tbody>
                                </table>
                                    <div class="form-actions2">
                                        <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button>
                                    </div>
                                <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div>
                </div>

                <div class="span3">
                </div>
            </div> <!-- row -->
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
            });

        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



