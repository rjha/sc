<?php
    include ('sc-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;
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

    $host = "http://".$_SERVER["HTTP_HOST"];
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

       <head><title> 3mik.com - login page</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/3p/zocial/css/zocial.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){

                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });

            });

        </script>


    </head>

     <body>

        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                </div>
            </div>


            <div class="row">
                <div class="span12">
                    <div class="page-header"> &nbsp; </div>
                    <div class="p10"> <?php FormMessage::render(); ?> </div>
                </div>
            </div> <!-- row -->

            <div class="row">
                <div class="span7">
                  <div id="mik-login-wrapper">
                   <h3> Login with 3mik account </h3>
                        <a href="/user/register.php"> Register for a new 3mik account</a> (Free and takes just 30 seconds!)

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
                                    &nbsp;<a href="/user/account/mail-password.php">Forgot your password?</a>

                                </td>
                            </tr>

                        </table>

                        <div class="form-actions">
                            <button class="btn btn-primary" type="submit" name="login" value="Login" onclick="this.setAttribute('value','Login');" ><span>Login</span></button>
                            <a href="<?php echo $qUrl; ?>">
                                <button class="btn" type="button" name="cancel"><span>Cancel</span></button>
                            </a>

                        </div>

                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                    </form>
                   </div>

                </div> <!-- span51 -->
                <div class="span4 social-buttons p20">
                    <div class="row">
                       <h3> - OR - </h3>
                    </div> <!-- row1 -->
                    <div class="row">
                        <div class="zocial facebook mt10">
                               <a href="<?php echo $fbDialogUrl; ?>">Login with Facebook</a>
                        </div>
                    </div> <!-- row2 -->

                    <div class="row">
                        <div class="zocial google mt10">
                            <a href="<?php echo $googleAuthUrl; ?>">Login with Google</a>&nbsp;&nbsp;
                        </div>
                    </div> <!-- row3 -->
                     <div class="row">
                        <div class="zocial twitter mt10">
                            <a href="/user/twitter-login.php">Login with Twitter</a>&nbsp;
                        </div>
                    </div> <!-- row3 -->

                </div> <!-- span4 -->
            </div> <!-- row -->

       </div> <!-- container -->

        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>

    </body>
</html>
