<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/user/dashboard/posts.php" => "Posts",
                "/user/dashboard/comments.php" => "Comments",
                "/user/dashboard/activities.php" => "Activity",
                "/user/dashboard/bookmark.php" => "Saved",
                "/user/dashboard/following.php" => "Following"
               );


echo  \com\indigloo\sc\ui\Tabs::getHtml($tabUrlMap);

?>