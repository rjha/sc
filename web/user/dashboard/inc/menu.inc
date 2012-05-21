<?php

$tabs = array('posts','comments','profile','likes','saves');
$activeData = array();
foreach($tabs as $tab) {
    if($tab == $activeTab )
        $activeData[$tab] = 'active' ;
    else
        $activeData[$tab] = '' ; 
}

?>

<ul class="nav nav-tabs">
   <li class="<?php echo $activeData['posts']; ?>"> <a href="/user/dashboard/posts.php">Posts</a></li>
   <li class="<?php echo $activeData['comments']; ?>"> <a href="/user/dashboard/comments.php">Comments</a></li>
   <li class="<?php echo $activeData['profile']; ?>"> <a href="/user/dashboard/profile.php">Profile</a></li>
   <li class="<?php echo $activeData['likes']; ?>"> <a href="/user/dashboard/bookmark.php?code=1">Likes</a></li>
   <li class="<?php echo $activeData['saves']; ?>"> <a href="/user/dashboard/bookmark.php?code=2">Favorites</a></li>
</ul>
