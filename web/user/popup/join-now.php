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

<p class="comment-text">
    You can use your existing facebook, google or twitter account to sign up.
    You can also sign up using your email.
</p>

<div id="zocial-grid">
    <div class="grid-row"> 
        <div class="column">
            <a class="zocial facebook" href="<?php echo $fbDialogUrl; ?>">&nbsp;&nbsp;Facebook</a>&nbsp;
        </div>

        <div class="column">
            <a class="zocial twitter" href="/user/twitter-login.php">&nbsp;&nbsp;Twitter</a>&nbsp;
        </div>
    </div>
    <div class="clear"> </div>

    <div class="grid-row"> 

        <div class="column">
             <a class="zocial gmail" href="<?php echo $googleAuthUrl; ?>">&nbsp;&nbsp;Google</a>&nbsp;
        </div>

        <div class="column">
            <a class="btn" href="/user/register.php">Email Sign up&nbsp;&raquo;</a>
        </div>
    </div>
    <div class="clear"> </div>

    <div class="grid-row"> 
        <div class="column2">
            Already have a 3mik account?
            <a href="/user/login.php">login</a>
        </div>
    </div>

</div> <!-- zocial-grid -->



