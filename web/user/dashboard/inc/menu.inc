<style>

#user-menu {
    height: 40px;
    line-height: 37px;
    padding: 0;
    position: relative;
    z-index: 102;
    background: white;
    /* border-bottom: 1px solid #E5E5E5; */
    margin-bottom: 10px;
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
    border-top : 1px solid #e5e5e5 ;
    border-bottom : 1px solid #e5e5e5 ;
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

</style>

<div id="user-menu">
    <div class="item"><a href="#">Dashboard</a></div>
    <div class="item1"> <a href="/user/dashboard/posts.php">My items</a></div>
    <div class="item"> <a href="/user/dashboard/posts.php">Saved items</a></div>
    <div class="item"><a href="/user/dashboard/list/index.php">Lists</a></div>
    
    <div class="item"><a href="#">Activities</a></div>
    <div class="item"><a href="#">Settings</a></div>

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

        
