<?php

    //Active tab depends on rquest URI
    $urlMap  = array(
    "/user/dashboard/index.php" => "Dashboard",
    "/user/dashboard/posts.php" => "My items",
    "/user/dashboard/bookmark.php" => "Saved items",
    "/user/dashboard/list/index.php" => "Lists",
    "/user/dashboard/list/detail.php" => "Lists",
    "/user/dashboard/activities.php" => "Activity",
    "/user/dashboard/mails.php" => "Settings");

    $classMap = array("active" => "item1", "normal" => "item");
    $template = "/fragments/ui/links/umenu.tmpl"; 
    $umenuHtml =   \com\indigloo\sc\ui\Links::getHtml($urlMap,$template,$classMap);


?>

<style>

#user-menu {
    height: 40px;
    line-height: 37px;
    padding: 0;
    position: relative;
    z-index: 102;
    background: white;
    margin-bottom: 10px;
    /* border-bottom : 1px solid #e5e5e5 ;  */
}

#user-menu .item {
    float:left ;
    padding-left:10px;
    padding-right:10px;
}

#user-menu .item1 {
    float:left ;
    border-bottom: 3px solid #DD4B39;
    padding: 0 4px;
    margin: 1px 12px 0;
    font-weight: bold;
}

#user-menu .item1 a {
      color: #DD4B39;
}

#user-menu .item a {
    color: #333;
}

#user-menu .item a:hover {
    text-decoration: underline ;
}

#page-action {
   
    border-bottom : 1px solid #f1f1f1 ;
    border-top: 1px solid #f1f1f1  ;
}


#top-unit a {
    color: #666;
    background: #F7F7F7;
    font-weight: bold;
    font-family: arial,sans-serif;
    font-size: 13px;
    border: 1px solid #CECECE;
}

#top-unit {
    background-color: #F7F7F7;
}

.widget {
    border-bottom: 1px solid #f1f1f1 ;
}

.widget .options .links { float:right;}
.action-form {
    display: none ;
}

.btn-flat {

    text-shadow: none;
    padding: 2px 20px 2px 20px; 
    border-radius: 5px;
    color: #666;
    background: white;
    font-weight: bold;
    font-family: arial,sans-serif;
    font-size: 13px;
    border: 1px solid #CECECE;

}

.action-form .floatr {
    border-bottom: 1px solid #28c ;
}

</style>

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
                <li><a href="/user/dashboard/profile.php">Edit profile</a></li>
                <?php echo $passwordUrl; ?>
            </ul>
          </li>

        </ul>

    </div>

</div>

        
