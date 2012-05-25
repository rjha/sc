<?php

include('sc-app.inc');
include(APP_CLASS_LOADER);


if($argc < 4) { 
    printf("Usage : $php change.php <userid> <email> <password> \n");
    exit ; 
}


$userId = $argv[1];
$email = $argv[2];
$password = $argv[3] ;

$data = \com\indigloo\auth\User::changePassword('sc_user',$userId,$email,$password) ;


