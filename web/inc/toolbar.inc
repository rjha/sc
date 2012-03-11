<?php

 use \com\indigloo\sc\auth\Login as Login;
 
 if(Login::isValid()){
    $gSessionLogin = Login::getLoginInSession();
    include($_SERVER['APP_WEB_DIR'].'/inc/toolbar/user.inc') ;

  } else {

    include($_SERVER['APP_WEB_DIR'].'/inc/toolbar/default.inc') ;
  }
  
  
?>
