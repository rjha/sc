<?php 

    //sc/user/account/login-now.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    $title = "Go to login page";
    $header = "Your password has been changed.";
    $goText = "Go to Login page &rarr;" ;
    $goUrl = "/user/login.php" ;

    include($_SERVER['APP_WEB_DIR'] . '/site/go-static.php');



?>
