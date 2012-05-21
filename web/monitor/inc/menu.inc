          

<?php

$tabs = array('posts','comments','groups','feedbacks','users');
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
   <li  class="<?php echo $activeData['users']; ?>"><a href="/monitor/users.php">Users</a></li>
   <li  class="<?php echo $activeData['groups']; ?>"><a href="/monitor/groups.php">Groups</a></li>
   <li  class="<?php echo $activeData['feedbacks']; ?>"><a href="/monitor/feedback.php">Feedback</a></li>
</ul>
