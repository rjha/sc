<?php

    use \com\indigloo\sc\auth\Login as Login;
    $gSessionLogin = Login::tryLoginInSession();
    
    if(!is_null($gSessionLogin)) {
        include(APP_WEB_DIR.'/inc/toolbar/slim/user.inc') ;
    } else {
        include(APP_WEB_DIR.'/inc/toolbar/slim/default.inc') ;
    } 

?>