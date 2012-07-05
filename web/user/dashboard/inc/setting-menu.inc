<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/user/dashboard/profile.php" => "Profile",
                "/user/dashboard/mails.php" => "Mails");

if(\com\indigloo\sc\auth\Login::hasMikLogin()) {
   $tabUrlMap["/user/account/change-password.php"] =  "Password" ;
}
echo  \com\indigloo\sc\ui\Tabs::getHtml($tabUrlMap);

?>