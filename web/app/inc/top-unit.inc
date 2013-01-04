<?php

    use \com\indigloo\app\auth\Login as Login;
    $loginId = Login::tryLoginIdInSession();
    
    if(!empty($loginId)) {
        include(APP_WEB_DIR.'/app/inc/top/user.inc') ;
    } else {
        include(APP_WEB_DIR.'/app/inc/top/default.inc') ;
    } 

?>