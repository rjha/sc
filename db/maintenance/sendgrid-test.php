<?php
include('sc-app.inc');
include(APP_CLASS_LOADER);



$tos = array("jha.rajeev@gmail.com") ;
$from = "support@3mik.com" ;
$fromName = " 3mik support" ;
$subject =" test from command line # 1 ";
$text = " test text #1 ";
$html = " test html #1 ";


$code = \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
printf(" mail wrapper returned %d ", $code);

?>
