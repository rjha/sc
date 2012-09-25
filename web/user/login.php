<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    use com\indigloo\ui\form\Message as FormMessage;

     $gWeb = \com\indigloo\core\Web::getInstance();
    //do we already have a login?
    if(\com\indigloo\sc\auth\Login::hasSession()) {
        header("Location: / ");
    }

    //qUrl and fUrl
    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
    //$qUrl = urldecode($qUrl);

    // should login do some action?
    $gSessionAction = Url::tryQueryParam("g_session_action");
    if(!empty($gSessionAction)) {
        $gWeb->store("global.session.action",$gSessionAction);
    }

    $fUrl = Url::current();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $stoken = Util::getMD5GUID();

    $gWeb->store("mik_state_token",$stoken);

    //Facebook OAuth2
    $fbAppId = Config::getInstance()->get_value("facebook.app.id");

    $host = Url::base();
    $fbCallback = $host."/callback/fb2.php" ;

    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email&state=".$stoken ;

    //Google OAuth2
    $googleClientId = Config::getInstance()->get_value("google.client.id");
    $googleCallback = $host. "/callback/google2.php" ;

    $googleAuthUrl  = "https://accounts.google.com/o/oauth2/auth?scope=" ;
    //space delimited scope
    $googleScope =  "https://www.googleapis.com/auth/userinfo.email" ;
    $googleScope =   $googleScope.Constants::SPACE."https://www.googleapis.com/auth/userinfo.profile" ;
    $googleAuthUrl .= urlencode($googleScope);

    $googleAuthUrl .= "&client_id=".$googleClientId ;
    $googleAuthUrl .= "&state=".$stoken ;
    $googleAuthUrl .= "&response_type=code" ;
    $googleAuthUrl .= "&redirect_uri=".urlencode($googleCallback) ;

?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - login page</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        
        <div class="container mh800">
            <div style="margin-top:100px;"> &nbsp; </div> <!-- row:1 -->
                
            <div class="row mt20">

                <div class="span3 offset1">
                    
            <div id="zocial-grid">
                <div class="column">
                    <a class="zocial facebook" href="<?php echo $fbDialogUrl; ?>">Facebook Sign in</a>&nbsp;
                </div>

                <div class="column">
                    <a class="zocial twitter" href="/user/twitter-login.php">Twitter Sign in</a>&nbsp;
                </div>
             

                <div class="column">
                     <a class="zocial gmail" href="<?php echo $googleAuthUrl; ?>">Google Sign in</a>&nbsp;
                </div>

            </div> <!-- zocial-grid -->



                </div> <!-- span3 -->

                <div class="span6" style="border-left:1px dashed #333333;padding-left:40px;">
                    <h3>3mik login</h3>
                    <div class="p10"> <?php FormMessage::render(); ?> </div>
                    <div class="lb1">
                        <form id="web-form1"  name="web-form1" action="/user/form/login.php" method="POST">
                            <div class="error">    </div>

                            <table class="form-table">
                                <tr>
                                    <td class="field">Email<span class="red-label">*</span></td>
                                    <td>
                                        <input type="text" name="email" maxlength="64" class="required" title="Email is required" value="<?php echo $sticky->get('email'); ?>"/>
                                    </td>
                                </tr>

                                 <tr>
                                    <td class="field"> Password<span class="red-label">*</span></td>
                                    <td>
                                        <input type="password" name="password" maxlength="32" class="required" title="Password is required" value=""/>
                                        
                                    </td>
                                </tr>

                            </table>

                            <div class="form-actions">
                                <button class="btn btn-primary" type="submit" name="login" value="Login" onclick="this.setAttribute('value','Login');" ><span>Login</span></button>
                                
                                &nbsp;&nbsp;
                                <a href="/user/account/mail-password.php">Forgot your password?</a>
                            </div>

                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                        </form>
                    </div> <!-- form wrapper -->
                    <div class="mt20">
                        <a href="/user/register.php">Register for a new 3mik account</a>&nbsp;(&nbsp;Free and takes only a minute! )
                    </div>
            </div> 

        </div> <!-- row:2 -->


       </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });

            });

        </script>

        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>

    </body>
</html>
