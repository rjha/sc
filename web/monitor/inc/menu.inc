<?php


$requestURI = $_SERVER['REQUEST_URI'] ;
$pos = strpos($requestURI, '?');
//remove the part after ? from Url
if($pos !== false) {
    $requestURI = substr($requestURI,0,$pos);
}

//Active tab depends on rquest URI
$tabURIMap  = array(
                "/monitor/index.php" => "posts",
                "/monitor/comments.php" => "comments",
                "/monitor/feed.php" => "activities",
                "/monitor/users.php" => "users",
                "/monitor/groups.php" => "groups",
                "/monitor/feedback.php" => "feedback");
    
$tabs = array('posts','comments', 'activities', 'users', 'groups','feedback');
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

   <li  class="<?php echo $activeData['posts']; ?>" ><a href="/monitor/index.php">Posts</a></li>
   <li  class="<?php echo $activeData['comments']; ?>"><a href="/monitor/comments.php">Comments</a></li>
   <li  class="<?php echo $activeData['activities']; ?>" ><a href="/monitor/feed.php">Activities</a></li>
   <li  class="<?php echo $activeData['users']; ?>"><a href="/monitor/users.php">Users</a></li>
   <li  class="<?php echo $activeData['groups']; ?>"><a href="/monitor/groups.php">Groups</a></li>
   <li  class="<?php echo $activeData['feedback']; ?>"><a href="/monitor/feedback.php">Feedback</a></li>
</ul>
