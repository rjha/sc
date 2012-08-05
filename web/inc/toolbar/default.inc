<?php
    // not logged in - so share/ask click should take you back to share/ask
    // after login is done.
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gomInSession = $gWeb->find("global.overlay.message",true);
    $goverlay = \com\indigloo\sc\html\Site::getOverlay($gomInSession);


?>


<div id="site-toolbar" class="navbar">

    <div class="row toolbar-row1">
        <div class="span6">
        <h1> <a class="brand" href="/">3mik.com <span class="beta">BETA</span> </a> </h1>

        </div>
        <div class="span6">
        <div class="floatr">
            <ul class="nav nav-pills">

            <li> <a href="/site/help.php">Help</a> </li>
            <li> <a href="/user/login.php">Login</a> </li>
            <li> <a href="/user/register.php">Register</a> </li>

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                About
                <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li> <a href="/site/contact.php">Contact</a> </li>
                    <li> <a href="/site/help.php">Help</a> </li>
                    <li> <a href="/site/copyright.php">Copyright</a> </li>
                </ul>
            </li>
            </ul>
        </div>
        </div>
    </div> <!-- row1 -->

    <div class="row toolbar-row2">
        <div class="span6">

        <ul class="nav nav-pills">

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Categories
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                            $categoryDao = new \com\indigloo\sc\dao\Category();
                            $map = $categoryDao->getIdNameMap();
                            foreach($map as $item) {
                                $strItem = sprintf("<li><a href=\"/category/%s\">%s</a></li>",$item['id'],$item['name']);
                                echo $strItem ;
                            }
                        ?>
                    </ul>
                </li> <!-- category -->

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Location
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/search/location/bangalore">Bangalore</a></li>
                        <li><a href="/search/location/chennai">Chennai</a></li>
                        <li><a href="/search/location/delhi">Delhi</a></li>
                        <li><a href="/search/location/hyderabad">Hyderabad</a></li>
                        <li><a href="/search/location/kolkata">Kolkata</a></li>
                        <li><a href="/search/location/mumbai">Mumbai</a></li>
                        <li><a href="/search/location/pune">Pune</a></li>
                    </ul>
                </li> <!-- location -->

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        More
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/recent">New items</a></li>
                        <li><a href="/editor/picks">Editor's pick</a></li>
                        <li><a id="nav-open-group" href="">Popular Groups</a></li>
                        <li><a href="/surprise/me">Surprise me!</a></li>
                    </ul>
                </li> <!-- more -->
                <li class="active"> <a id="nav-open-share" href="">#&nbsp;Share</a></li>
                </ul>

        </div>
        <div class="span2">
            <form method="GET" action="/search/site">
                <input id="site-search" name="gt" type="text" class="search-query" placeholder="Search">
            </form>
        </div>
        <div class="span4">
            &nbsp;
        </div>

    </div> <!-- row2 -->

    <div class="row p10">
        <div class="span6 offset2">
        <span class="comment-text"> Discover and share with 3mik </span>
        &nbsp;<a class="nav-learn-more" href="/site/about.php"><span class="badge">Learn more&nbsp;&raquo;</span></a>

        </div>
        <div style="float:right;padding-bottom:5px;">
                <span style="font-size: 16px; padding-right: 5px;">&nbsp;</span>
                <a href="http://twitter.com/3mikindia"><img src="/css/images/ico_twitter.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Twitter"></a>
                <a href="http://www.facebook.com/3mikindia"><img src="/css/images/ico_facebook.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Facebook"></a>
                <a href="http://plus.google.com/108668124722035129135"><img src="/css/images/ico_googleplus.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Google+"></a>
        </div> <!-- social buttons -->

    </div> <!-- row3 -->
    <div class="row">
        <?php if(!empty($goverlay)) { echo $goverlay ; } ?>
    </div>

</div> <!-- toolbar -->




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

</div> <!-- simple popup -->

<div id="popup-mask"> </div>


<div id="nav-share">
    <a href="/share/new.php">
        <div class="stack">
            <div class="header">Upload from computer</div>
            <p>&nbsp;</p>
        </div>
    </a>
    <a href="/qa/external/image.php">
        <div class="stack">
            <div class="header">Share a webpage</div>
            <p> select this option if you want to share images from a webpage </p>
        </div>
    </a>
</div> <!-- share-popup for navigation -->




