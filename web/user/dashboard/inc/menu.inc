<?php


$requestURI = $_SERVER['REQUEST_URI'] ;
$pos = strpos($requestURI, '?');

//remove the part after ? from Url
if($pos !== false) {
    $requestURI = substr($requestURI,0,$pos);
}

//Active tab depends on rquest URI
$tabURIMap  = array(
                "/user/dashboard/activities.php" => "activity",
                "/user/dashboard/bookmark.php" => "bookmark",
                "/user/dashboard/comments.php" => "comment",
                "/user/dashboard/following.php" => "following",
                "/user/dashboard/follower.php" => "following",
                "/user/dashboard/posts.php" => "post");
    
$tabs = array('post','comment','activity','bookmark', 'following');
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
   <li class="<?php echo $activeData['post']; ?>"> <a href="/user/dashboard/posts.php">Posts</a></li>
   <li class="<?php echo $activeData['comment']; ?>"> <a href="/user/dashboard/comments.php">Comments</a></li>
   <li class="<?php echo $activeData['activity']; ?>"> <a href="/user/dashboard/activities.php">Activities</a></li>
   <li class="<?php echo $activeData['bookmark']; ?>"> <a href="/user/dashboard/bookmark.php">Favorites</a></li>
   <li class="<?php echo $activeData['following']; ?>"> <a href="/user/dashboard/following.php">Following</a></li>
   
</ul>
