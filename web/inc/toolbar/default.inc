<?php
$params = array('q' => urlencode(\com\indigloo\Url::current()));
$shareUrl = \com\indigloo\Url::createUrl('/share/new.php',$params);
$askUrl = \com\indigloo\Url::createUrl('/qa/new.php',$params);
?>

<div id="header">
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <div class="pull-left">
                    <a class="brand" href="/">3mik.com  <span class="beta">BETA</span> </a>
                </div>
                <div class="pull-left">
                    <ul class="nav">
                        <li class="active"> <a href="/">Home</a> </li>
                        <li class="active"><a href="<?php echo $shareUrl; ?>">Share</a></li>
                        <li class="active"><a href="<?php echo $askUrl; ?>">Ask</a></li>
                        <li class="active"><a href="/user/login.php">Login</a></li>
                        <li class="divider-vertical"></li>
                    </ul> <!-- links -->
                </div>
                <div class="pull-left"> 
                    <form method="GET" action="/search/site">
                        <input id="site-search" name="gt" type="text" class="search-query" placeholder="Search">
                    </form>
                </div>
          </div>
        </div>
    </div> <!-- navbar -->
</div> <!-- header -->

