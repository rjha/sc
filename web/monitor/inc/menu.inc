<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/monitor/index.php" => "posts",
                "/monitor/comments.php" => "comments",
                "/monitor/feed.php" => "activities",
                "/monitor/users.php" => "users",
                "/monitor/groups.php" => "groups",
                "/monitor/feedback.php" => "feedback");

$tabCss = \com\indigloo\sc\ui\Tabs::getActiveTabClass($tabUrlMap,"active");

?>

<ul class="nav nav-tabs">

   <li  class="<?php echo $tabCss['posts']; ?>" ><a href="/monitor/index.php">Posts</a></li>
   <li  class="<?php echo $tabCss['comments']; ?>"><a href="/monitor/comments.php">Comments</a></li>
   <li  class="<?php echo $tabCss['activities']; ?>" ><a href="/monitor/feed.php">Activities</a></li>
   <li  class="<?php echo $tabCss['users']; ?>"><a href="/monitor/users.php">Users</a></li>
   <li  class="<?php echo $tabCss['groups']; ?>"><a href="/monitor/groups.php">Groups</a></li>
   <li  class="<?php echo $tabCss['feedback']; ?>"><a href="/monitor/feedback.php">Feedback</a></li>
</ul>
