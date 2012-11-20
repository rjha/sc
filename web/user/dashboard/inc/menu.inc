<?php

    //Active tab depends on rquest URI
    $urlMap  = array(
    "/user/dashboard/index.php" => "Dashboard",
    "/user/dashboard/posts.php" => "My items",
    "/user/dashboard/bookmark.php" => "Likes",
    "/user/dashboard/list/index.php" => "Lists",
    "/user/dashboard/list/detail.php" => "Lists",
    "/user/dashboard/activities.php" => "Activity",
    "/user/dashboard/mails.php" => "Settings",
    "/user/dashboard/profile.php" => "Settings",
     "/user/account/edit.php" => "Settings",
    "/user/account/change-password.php" => "Settings");

    $classMap = array("active" => "item1", "normal" => "item");
    $template = "/fragments/ui/links/umenu.tmpl"; 
    $umenuHtml =   \com\indigloo\sc\ui\Links::getHtml($urlMap,$template,$classMap);


?>

<link rel="stylesheet" type="text/css" href="/css/extra.css" >
<div id="user-menu">
    <?php echo $umenuHtml;  ?>
    <div class="item">
        <ul class="nav nav-pills">
            <li class="dropdown">
            <a class="dropdown-toggle"
               data-toggle="dropdown"
               href="#">
                More
                <b class="caret"></b>
              </a>
            <ul class="dropdown-menu">
                <li> <a href="/user/dashboard/comments.php">Comments</a></li>
                <li><a href="/user/dashboard/follower.php">Followers</a></li>
                <li><a href="/user/dashboard/following.php">Following</a></li>
                
            </ul>
          </li>

        </ul>

    </div>

</div>

        
