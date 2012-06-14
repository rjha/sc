<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/user/dashboard/activities.php" => "activity",
                "/user/dashboard/bookmark.php" => "bookmark",
                "/user/dashboard/comments.php" => "comment",
                "/user/dashboard/following.php" => "following",
                "/user/dashboard/follower.php" => "following",
                "/user/dashboard/posts.php" => "post");


$tabCss = \com\indigloo\sc\ui\Tabs::getActiveTabClass($tabUrlMap,"active");

?>

<ul class="nav nav-tabs">
   <li class="<?php echo $tabCss['post']; ?>"> <a href="/user/dashboard/posts.php">Posts</a></li>
   <li class="<?php echo $tabCss['comment']; ?>"> <a href="/user/dashboard/comments.php">Comments</a></li>
   <li class="<?php echo $tabCss['activity']; ?>"> <a href="/user/dashboard/activities.php">Activities</a></li>
   <li class="<?php echo $tabCss['bookmark']; ?>"> <a href="/user/dashboard/bookmark.php">Favorites</a></li>
   <li class="<?php echo $tabCss['following']; ?>"> <a href="/user/dashboard/following.php">Following</a></li>

</ul>
