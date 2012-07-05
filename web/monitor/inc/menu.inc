<?php

//Active tab depends on rquest URI
$tabUrlMap  = array(
                "/monitor/index.php" => "posts",
                "/monitor/comments.php" => "comments",
                "/monitor/feed.php" => "activities",
                "/monitor/users.php" => "users",
                "/monitor/groups.php" => "groups",
                "/monitor/feedback.php" => "feedback");

echo  \com\indigloo\sc\ui\Tabs::getHtml($tabUrlMap);

?>
