<?php
    $params = array('q' => urlencode(\com\indigloo\Url::current()));
    $shareUrl = \com\indigloo\Url::createUrl('/share/new.php',$params);
    $askUrl = \com\indigloo\Url::createUrl('/qa/new.php',$params);
    $monitorUrl = '' ;
    if(\com\indigloo\sc\auth\Login::isAdmin()) {
        $monitorUrl = '<li><a href="/monitor/index.php">Monitor App</a></li>';
    }

    $pubUserId = \com\indigloo\sc\util\PseudoId::encode($gSessionLogin->id) ;

?>

<div id="site-toolbar">

    <div class="row toolbar-row1">
        <div class="span6">
        <h1> <a class="brand" href="/">3mik.com <span class="beta">BETA</span> </a> </h1>

        </div>
        <div class="span6">
        <div class="floatr">
            <ul class="nav nav-pills">

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $gSessionLogin->name; ?><b class="caret"></b> </a>
                <ul class="dropdown-menu">
                    <li><a href="/user/dashboard/posts.php">Account</a></li>
                    <li><a href="/user/dashboard/profile.php">Settings</a></li>
                    <li><a href="/pub/user/<?php echo $pubUserId; ?>">Public Profile</a></li>

                    <?php echo $monitorUrl; ?>
                    <li class="divider"></li>
                    <li><a href="/user/logout.php">Logout</a></li>
                </ul>
            </li> <!-- user menu -->
            <li class="divider-vertical"></li>
            <li> <a href="/site/about.php">Help</a> </li>
            <li> <a href="/site/contact.php">Contact</a> </li>
            <li class="divider-vertical"></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                About
                <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li> <a href="/site/contact.php">Contact</a> </li>
                    <li> <a href="/site/about.php">Help</a> </li>
                    <li> <a href="#">Copyright</a> </li>
                </ul>
            </li> <!-- About -->
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
                        More filters
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/recent">New items</a></li>
                        <li><a href="/editor/picks">Editor's pick</a></li>
                        <li><a id="nav-group-open" href="">Popular Groups</a></li>
                        <li><a href="/surprise/me">Surprise me!</a></li>
                    </ul>
                </li> <!-- more -->
                <li class="active"> <a href="<?php echo $shareUrl; ?>">Share</a></li>
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

    <div class="row p10 mb20">
        <div class="span6 offset2">
        <span class="comment-text"> Discover and share with 3mik </span>
        &nbsp;<a href="/site/about.php">Learn more&nbsp;&raquo;</a>

        </div>
        <div style="float:right;padding-bottom:5px;">
                <span style="font-size: 16px; padding-right: 5px;">&nbsp;</span>
                <a href="http://twitter.com/3mikindia"><img src="/css/images/ico_twitter.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Twitter"></a>
                <a href="http://www.facebook.com/3mikindia"><img src="/css/images/ico_facebook.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Facebook"></a>
                <a href="http://plus.google.com/108668124722035129135"><img src="/css/images/ico_googleplus.png" width="32" height="32" style="vertical-align: middle; padding-left: 2px;" alt="Google+"></a>
        </div> <!-- social buttons -->

    </div> <!-- row3 -->

</div>


