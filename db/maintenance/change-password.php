<?php

include('sc-app.inc');
include(APP_CLASS_LOADER);

use \com\indigloo\sc\util\PseudoId as PseudoId;

if($argc < 3) { 
    printf("Usage : $php change.php <pseudo_id> <password> \n");
    exit ; 
}


$pseudoId = $argv[1];
$pseudoId = trim($pseudoId);

$password = $argv[2] ;
$loginId = PseudoId::decode($pseudoId);

//get email lookup on loginId
$userDao = new \com\indigloo\sc\dao\User();
$row = $userDao->getOnLoginId($loginId);
$email = $row["email"];

printf("change for login_id = %s, email = %s \n ",$loginId,$email);
$data = \com\indigloo\auth\User::changePassword("sc_user",$loginId,$email,$password) ;


