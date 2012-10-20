<?php

    //sc/user/account/mail-password.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Util;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

?>

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Share your find, need and knowledge</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
         <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span9">


                    <div class="page-header">
                        <h2> Forgot your password? </h2>
                    </div>

                    <?php FormMessage::render(); ?>

                    <form  id="web-form1"  name="web-form1" action="/user/account/form/mail-password.php" enctype="multipart/form-data"  method="POST">
                        <div class="row">
                            <div class="span9">
                                <div id="image-uploader"> </div>
                            </div>
                        </div> <!-- top row -->
                        <table class="form-table">
                            <tr>
                                <td> <label>Enter your email address</label>
                                <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                            </tr>

                            <tr>
                                <td>
                                    <p>Fill in your email address.
                                       We will mail you instructions to reset your password.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-actions">
                                        <button class="btn gBtnUp" type="submit" name="save" value="Save"><span>Submit</span></button>
                                    </div>

                                </td>
                            </tr>

                        </table>

                        <input type="hidden" name="q" value="<?php echo Url::current(); ?>" />

                    </form>


                </div> <!-- span9 -->

                <div class="span3">
                    <!-- sidebar -->

                </div>

            </div> <!-- row -->

        </div> <!-- container -->



        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                $("#web-form1").validate({
                       errorLabelContainer: $("#web-form1 div.error")
                });
            });


        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
