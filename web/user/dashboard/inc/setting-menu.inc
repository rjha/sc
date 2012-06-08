<?php

$tabs = array('profile','password');
$activeData = array();
foreach($tabs as $tab) {
    if($tab == $activeTab )
        $activeData[$tab] = 'active' ;
    else
        $activeData[$tab] = '' ;
}

?>

<ul class="nav nav-tabs">
   <li class="<?php echo $activeData['profile']; ?>"> <a href="/user/dashboard/profile.php">Profile</a></li>
   <li class="<?php echo $activeData['password']; ?>"> <a href="/user/account/change-password.php">Change Password</a></li>

</ul>
