<?php

    //sc/user/account/change-password.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\exception\UIException ;

    $gSessionLogin = Login::getLoginInSession();
    $loginId = $gSessionLogin->id ;

    if(strcmp($gSessionLogin->provider,Login::MIK) != 0 ) {
        $message = "change password only works for 3mik logins!" ;
        throw new UIException(array($message));
    }

    $userDao = new \com\indigloo\sc\dao\User() ;
    $userDBRow = $userDao->getonLoginId($loginId);

    //tokens for use in next screen
    $ftoken = Util::getMD5GUID();
    $email = $userDBRow["email"];
    $femail = Util::encrypt($email);
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->store("change.password.email",$femail);
    $gWeb->store("change.password.token",$ftoken);

    $title = $userDBRow["email"];


    $qUrl = base64_encode(Url::current());
    $fUrl = Url::current();
    $submitUrl = "/user/account/form/change-password.php" ;

?>

<!DOCTYPE html>
<html>

       <head>
        <title> Change password - <?php echo $title; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

     <body>
         <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">

            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                 <div class="span12">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>

            </div>
            <div class="row">
                <div class="span11 offset1">
                    <div class="page-header">
                        <span style="padding-left:20px;padding-right:20px;">Change password</span>
                        <span>
                            <a class="btn-flat" href="/user/dashboard/mails.php">Mail preferences</a>
                        </span>
                        <span>
                            <a class="btn-flat" href="/user/dashboard/profile.php">Edit profile</a>
                        </span>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span8 offset1">
                   
                    <div class="faded-text mb20">
                        Please select a new password and click on Submit
                    </div>

                    <?php \com\indigloo\ui\form\Message::render(); ?>

                    <form id="web-form1"  name="web-form1" action="<?php echo $submitUrl; ?>" enctype="multipart/form-data"  method="POST">

                        <div class="error">    </div>

                        <table class="form-table">

                            <tr>
                                <td class="field">Password<span class="red-label">*</span> &nbsp; </td>
                                <td> <input id="password" type="password" name="password" maxlength="32" class="required" minlength="8" title="password should be atleast 8 chars!" value="" /></td>
                            </tr>

                            <tr>
                                <td class="field">Confirm Password <span class="red-label">*</span> &nbsp;</td>
                                <td> <input id="password_again" type="password" name="password_again" maxlength="32" class="required" minlength="8"  title="passwords do not match" value="" /></td>
                            </tr>

                        </table>

                        <div class="form-actions">
                            <button class="btn" type="submit" name="save" value="Save"><span>Save</span></button>
                           
                        </div>
                        <input type="hidden" name="ftoken" value="<?php echo $ftoken; ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                    </form>
                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){

                webgloo.sc.toolbar.add();

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error"),
                    rules: {
                        password: "required",
                        password_again: {
                            equalTo: "#password"
                        }
                    }
                });

            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


