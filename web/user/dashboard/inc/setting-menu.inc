<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/user/dashboard/profile.php" => "profile",
                "/user/account/change-password.php" => "password",
                "/user/dashboard/mails.php" => "mails");

$tabCss = \com\indigloo\sc\ui\Tabs::getActiveTabClass($tabUrlMap,"active");

?>

<ul class="nav nav-tabs">
   <li class="<?php echo $tabCss['profile']; ?>"> <a href="/user/dashboard/profile.php">Profile</a></li>
   <li class="<?php echo $tabCss['password']; ?>"> <a href="/user/account/change-password.php">Password</a></li>
   <li class="<?php echo $tabCss['mails']; ?>"> <a href="/user/dashboard/mails.php">Mails</a></li>
</ul>
