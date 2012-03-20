<?php

    use \com\indigloo\sc\auth\Login as Login;
    $gSessionLogin = Login::tryLoginInSession();
    
    if(!is_null($gSessionLogin)) {
        include($_SERVER['APP_WEB_DIR'].'/inc/toolbar/user.inc') ;
    } else {
        include($_SERVER['APP_WEB_DIR'].'/inc/toolbar/default.inc') ;
    } 

?>
