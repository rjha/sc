<?php


$requestURI = $_SERVER['REQUEST_URI'] ;
$pos = strpos($requestURI, '?');

//remove the part after ? from Url
if($pos !== false) {
    $requestURI = substr($requestURI,0,$pos);
}

//Active tab depends on rquest URI
$tabURIMap  = array(
                "/user/dashboard/profile.php" => "profile",
                "/user/account/change-password.php" => "password");
    
$tabs = array('profile','password');
$activeTab = $tabURIMap[$requestURI];

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
