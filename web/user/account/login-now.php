<?php

    //sc/user/account/login-now.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    $title = "Go to login page";
    $header = "Your password has been changed.";
    $goText = "Go to Login page" ;
    $goUrl = "/user/login.php" ;

    include(APP_WEB_DIR . '/site/go-automatic.php');



?>
