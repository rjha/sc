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
    padding: 10px 0 10px 0 ;
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
    padding: 4px 12px 4px 12px; 
    /* border-radius: 5px; */
    color: #333;
    background: white;
    /* font-weight: bold; */
    font-family: arial,sans-serif;
    font-size: 13px;
    /* border: 1px solid #CECECE; */
    border:1px solid #ccc;
}

.btn-flat:hover {
    background-color: #f7f7f7 ;
    /* text-decoration: underline ; */
    color: #222 ;
}


.action-form .floatr {
    border-bottom: 1px solid #28c ;
}

#uwidgets {
    padding:10px;
    width : 450px;
    min-height:800px;
}

.uwidget {
    width:450px;
    margin-top : 20px;
    background-color: white ;
}

.uwidget .wrapper {
    border-bottom : 1px solid #e6e6e6 ;
}

.uwidget .wrapper .photo {
    float :left ;
}

.uwidget .wrapper .nophoto {
    float :left ;
    width:100px;
    height:70px;
    margin:10px;
    border:1px solid #ccc ;
}

.uwidget .wrapper .photo img {
    width: auto;
    height: auto;
    max-width: 120px;
    max-height: 90px;
    padding: 5px;

}

.uwidget .wrapper .detail{
    /* height: 130px; */
    margin-left:140px;
}


.uwidget .wrapper .detail .name {
    max-height : 70px;
    overflow :hidden;
    padding : 10px;
}

.uwidget .wrapper .detail .name .title {
    color: #222;
    font-size: 16px;
    font-weight: bold;
    line-height: 1em;
    padding-bottom: 10px;
    font-family: arial;
}

.uwidget .wrapper .detail .link {
    height: 30px;
    padding-top:5px;
    padding-bottom:5px;
    padding-left:10px;
}

.uwidget .bottom {
    padding-top : 5px;
    padding-bottom :5px;
    
}


.uwidget .bottom .square {
    margin-left:10px;
    height:12px;
    width:12px ;
    /* background-color: #f5be00 ; */
    background-color: #DD4B39;
}

.uwidget a {
    cursor: pointer ;
}

.counter {
   
    color: #333 ;
    color: white ;
    color: #e09c15;
    color: white ;

    border: 1px solid white;
   
    
    background-color: #07823f;
  
    background-color: #fcfcfc;
   
    background-color: #b31e3b;
    background-color: #9aa1b4;
    

    background-color: #35c2e0;
    background-color: #57b20d;

    font-size: 13px;
    font-weight: normal;
    text-align: center;
    width : 120px;
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

        
