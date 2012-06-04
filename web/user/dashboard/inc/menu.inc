<?php

$tabs = array('posts','comments','activities','saves');
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
   <li class="<?php echo $activeData['activities']; ?>"> <a href="/user/dashboard/activities.php">Activities</a></li>
   <li class="<?php echo $activeData['saves']; ?>"> <a href="/user/dashboard/bookmark.php">Favorites</a></li>
</ul>
