<?php
    // not logged in - so share/ask click should take you back to share/ask
    // after login is done.
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gomInSession = $gWeb->find("global.overlay.message",true);
    $goverlay = \com\indigloo\sc\html\Site::getOverlay($gomInSession);

    $params = array('q' => base64_encode("/share/new.php"));
    $shareUrl = \com\indigloo\Url::createUrl('/share/new.php',$params);

    $params = array('q' => base64_encode("/qa/external/image.php"));
    $externalPageUrl = \com\indigloo\Url::createUrl('/qa/external/image.php',$params);
    
    

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
                    <li> <a href="/user/login.php">Login / Register</a> </li>
                    
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        About
                        <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li> <a href="/site/about.php">what is 3mik</a> </li>
                             <li> <a href="/site/help.php">Help</a> </li>
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
            <div class="header">Share a web page</div>
            <p> select this option if you want to share images from a web page </p>
        </div>
    </a>
</div> <!-- share popup -->




