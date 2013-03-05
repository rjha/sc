<?php

    //sc/user/invite.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    
    use \com\indigloo\Util;
    use \com\indigloo\Url;
    use \com\indigloo\ui\form\Sticky;
    
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = Url::current();

    $loginId = Login::tryLoginIdInSession() ;

    //add security token to form
    $formToken = Util::getBase36GUID();
    $gWeb->store("form.token",$formToken);

    $defaultMessage = \com\indigloo\sc\html\Site::getInvitationMessage();
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - invite your friends</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
 
    </head>

    <body>
        <style>
            /* @inpage @hardcoded remove hack */
            .form-table {width:90%;}

        </style>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span12">
                     <div class="page-header">
                        <h3> Send invitations</h3> 
                    </div>
                   
                </div>
            </div>
            <div class="row">
                <div class="span12">
                     <?php FormMessage::render(); ?>
                </div>

                <div class="span6">
                   
                    <form  id="web-form1"  name="web-form1" action="/user/action/invite.php" enctype="multipart/form-data"  method="POST">
                        
                        <table class="form-table">
                            <tr>
                                <td> <label>Emails (separate using comma)</label>
                                <input type="text"  name="email" class="required wp100" value="<?php echo $sticky->get('email'); ?>" />
                                
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label>Message*&nbsp;(max 512 chars)</label>
                                    <textarea  id="message" maxlength="512" name="message" class="required h130 wp100" cols="50" rows="4" ><?php echo $sticky->get('message',$defaultMessage); ?></textarea>
                                    <br>
                                   <span id="message_counter"></span>
                                </td>
                            </tr>
                            

                            <tr>
                                <td>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit" name="save" value="Save"><span>Submit</span></button>
                                        <a href="<?php echo base64_decode($qUrl); ?>"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

                                </td>
                               
                            </tr>

                        </table>

                        <input type="hidden" name="token" value="<?php echo $formToken; ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>



                </div> <!-- col:1 -->
                <div class="span6">   
                       &nbsp;

                </div> <!-- col:2 -->

            </div>

        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                $("#web-form1").validate({
                       errorLabelContainer: $("#web-form1 div.error")
                });

                webgloo.sc.util.addTextCounter("#message", "#message_counter");

            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
