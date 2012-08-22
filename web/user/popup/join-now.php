<?php 
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
   
    set_exception_handler('webgloo_ajax_exception_handler');

   
    $gWeb = \com\indigloo\core\Web::getInstance();
    //do we already have a login?
    if(\com\indigloo\sc\auth\Login::hasSession()) {
        $message = "You are already logged in!" ;
        echo $message ;
        exit ;
    }


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


 
<div class="row">
    <div class="span3">
        <div class="p10">
            <a class="zocial facebook" href="<?php echo $fbDialogUrl; ?>">Sign in with Facebook</a>
        </div>
    </div>
    <div class="span3">
        <div class="p10">
            <a class="zocial twitter" href="/user/twitter-login.php">Sign in with Twitter</a>&nbsp;
        </div>
    </div>

</div> <!-- row:1 -->

<div class="row">
  <div class="span3">
        <div class="p10">
            <a class="zocial gmail" href="<?php echo $googleAuthUrl; ?>">Sign in with Google</a>&nbsp;&nbsp;
        </div>

    </div>
    <div class="span3">
        <div class="p10">
            <a id="join-now-link" href="/user/register.php">Sign in with email&nbsp;&raquo;</a>
        </div>
    </div>

</div> <!-- row:2 -->

<div class="row">
  <div class="span3 offset3">
        <div class="p10">
           Have a 3mik account? <a href="/user/login.php">login now</a>
        </div>

    </div>
   
</div> <!-- row:3 -->

