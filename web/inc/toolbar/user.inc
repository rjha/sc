<?php

    $gWeb = \com\indigloo\core\Web::getInstance();
    $params = array('q' => urlencode(\com\indigloo\Url::current()));
    $shareUrl = \com\indigloo\Url::createUrl('/share/new.php',$params);
    $externalPageUrl = \com\indigloo\Url::createUrl('/qa/external/image.php',$params);
    
    $monitorUrl = '' ;
    $passwordUrl = '' ;

    if(\com\indigloo\sc\auth\Login::hasMikLogin()) {
        $passwordUrl = '<li><a href="/user/account/change-password.php">Change password</a></li>' ;
    }

    if(\com\indigloo\sc\auth\Login::isAdmin()) {
        $monitorUrl = '<li><a href="/monitor/index.php">Monitor App</a></li>';
    }

    $pubUserId = \com\indigloo\sc\util\PseudoId::encode($gSessionLogin->id) ;

    $gomInSession = $gWeb->find("global.overlay.message",true);
    $goverlay = \com\indigloo\sc\html\Site::getOverlay($gomInSession);


?>


<div id="site-toolbar" class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a> <!-- 3bars for smaller screens -->
            <a class="brand" href="/">3mik.com <span class="beta">BETA</span> </a>

            <div class="nav-collapse">
                <ul class="nav">
                    <li> <a href="/"><i class="icon-home"></i>&nbsp;Home</a></li>
                    <li> <a href="/site/help.php">Help</a> </li>
                     <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $gSessionLogin->name; ?><b class="caret"></b> </a>
                        <ul class="dropdown-menu">

                            <li>
                               <div id="submenu-col2"> 
                                   
                                    <ul>

                                        <li><a href="/user/dashboard/posts.php">Posts</a></li>
                                        <li><a href="/user/dashboard/comments.php">Comments</a></li>
                                        <li><a href="/user/dashboard/bookmark.php">Favorites</a></li>
                                        <li><a href="/user/dashboard/activities.php">Activities</a></li>
                                        <li><a href="/user/dashboard/following.php">Following</a></li>
                                        <li><a href="/user/dashboard/follower.php">Followers</a></li>
                                    </ul>
                                    <ul>

                                        <li><a href="/user/dashboard/profile.php">Edit profile</a></li>
                                        <li><a href="/user/dashboard/mails.php">Mail settings</a></li>
                                        <?php echo $passwordUrl; ?>
                                        <li><a href="/pub/user/<?php echo $pubUserId; ?>">Public page</a></li>
                                        <?php echo $monitorUrl; ?>
                                        <li><a href="/user/logout.php">Logout</a></li>
                                    </ul>

                                      
                                     
                                </div> <!-- 2-col submenu -->
                            </li>
                        </ul>
                    </li>  <!-- user menu -->

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        About
                        <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li> <a href="/site/about.php">About 3mik</a> </li>
                            <li> <a href="http://blog.3mik.com">Blog</a> </li>
                            <li> <a href="/site/contact.php">Contact</a> </li>
                            <li> <a href="/site/copyright.php">Copyright</a> </li>
                        </ul>
                    </li>

                </ul>
                <div id="navbar-search">
                    <form method="GET" action="/search/site">
                        <input name="gt" type="text"  placeholder="Search ...">
                    </form>
                </div>

            </div>

        </div>

    </div>
</div> <!-- top navigation bar -->

<div class="row">
    <?php if(!empty($goverlay)) { echo $goverlay ; } ?>
</div>

<div id="block-spinner"> </div>

<div id="simple-popup">
     <div id="header">
         <div class="wrapper">
            <div class="floatr">
                <a id="simple-popup-close" href="">close&nbsp;<i class="icon-remove"> </i></a>
            </div>
        </div>
        <div class="clear"> </div>
     </div>
    <div id="content"> </div>

</div>

<div id="popup-mask"> </div>
<!-- site simple popup -->

<div id="nav-share">
    <a href="<?php echo $shareUrl ?>">
        <div class="stack">
            <div class="header">Upload from computer</div>
            <p>&nbsp;</p>
        </div>
    </a>
    <a href="<?php echo $externalPageUrl ?>">
        <div class="stack">
            <div class="header">Share a webpage</div>
            <p> select this option if you want to share images from a webpage </p>
        </div>
    </a>
</div> <!-- share popup -->

