<?php

    //sc/user/register.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    //@see http://www.google.com/recaptcha
    include(WEBGLOO_LIB_ROOT . '/ext/recaptchalib.php');

    use com\indigloo\Util;
    use com\indigloo\Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $fUrl = Url::current();

    //add security token to form
    $formToken = Util::getBase36GUID();
    $gWeb->store("form.token",$formToken);


?>

<!DOCTYPE html>
<html>

    <head><title> User sign up page  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

   <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span8">
                    <div class="page-header">
                        <h2> Create a 3mik account</h2>
                    </div>
                    <p class="help-text">
                        Password should be atleast 8 characters.

                    </p>

                    <?php FormMessage::render(); ?>

                    <form id="web-form1"  name="web-form1" action="/user/form/register.php" enctype="multipart/form-data"  method="POST">

                        <div class="error">    </div>

                        <table class="form-table">

                             <tr>
                                <td class="field">First Name<span class="red-label">*</span></td>
                                <td>
                                    <input type="text" name="first_name" maxlength="32" class="required" title="&nbsp;First Name is required" value="<?php echo $sticky->get('first_name'); ?>"/>
                                </td>
                             </tr>
                              <tr>
                                <td class="field">Last Name<span class="red-label">*</span></td>
                                <td>
                                    <input type="text" name="last_name" maxlength="32" class="required" title="&nbsp;Last Name is required" value="<?php echo $sticky->get('last_name'); ?>"/>
                                </td>
                             </tr>

                              <tr>
                                <td class="field"> Email<span class="red-label">*</span></td>
                                <td>
                                    <input type="text" id="email" name="email" maxlength="64" class="required mail" title="&nbsp;Enter a valid email" value="<?php echo $sticky->get('email'); ?>"/>
                                </td>
                            </tr>


                             <tr>
                                <td class="field">Password<span class="red-label">*</span> &nbsp; </td>
                                <td> <input id="password" type="password" name="password" maxlength="32" class="required" minlength="8" title="password should be atleast 8 chars!" value="" /></td>
                            </tr>

                            <tr>
                                <td class="field">Confirm Password <span class="red-label">*</span> &nbsp;</td>
                                <td> <input id="password_again" type="password" name="password_again" maxlength="32" class="required" minlength="8"  title="passwords do not match" value="" /></td>
                            </tr>

                            <tr id="adrisya-wrapper">
                                <td class="field">Please leave this blank&nbsp;</td>
                                <td> <input name="adrisya_number" maxlength="10" value="" /></td>
                                
                            </tr>

                        </table>


                        <div class="form-actions">
                            <button class="btn btn-primary" type="submit" name="register" value="Register"><span>Sign in</span></button>

                        </div>
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        <input type="hidden" name="token" value="<?php echo $formToken; ?>" />

                    </form>
                </div>

            <div class="span4">
            
            </div>
        </div>
    </div> <!-- container -->

    <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

    <script type="text/javascript">
        $(document).ready(function(){
            //form validator
            //http://docs.jquery.com/Plugins/Validation/Methods/equalTo
            //new jquery validate plugin can accept rules

            $("#web-form1").validate({
                errorLabelContainer: $("#web-form1 div.error"),
                rules: {
                    password: "required",
                    password_again: {
                        equalTo: "#password"
                    },
                    email : {
                        required: true ,
                        email : true
                    }
                }
            });

            $("#adrisya-wrapper").hide();

        });

    </script>

    <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
    </div>

    </body>
</html>
