<?php

    $gWeb = \com\indigloo\core\Web::getInstance();
    $params = array('q' => urlencode(\com\indigloo\Url::current()));
    $shareUrl = \com\indigloo\Url::createUrl('/share/new.php',$params);
    $externalPageUrl = \com\indigloo\Url::createUrl('/qa/external/image.php',$params);
    $monitorUrl = '' ;
    if(\com\indigloo\sc\auth\Login::isAdmin()) {
        $monitorUrl = '<li><a href="/monitor/index.php">Monitor App</a></li>';
    }

    $pubUserId = \com\indigloo\sc\util\PseudoId::encode($gSessionLogin->id) ;

    $gomInSession = $gWeb->find("global.overlay.message",true);
    $goverlay = \com\indigloo\sc\html\Site::getOverlay($gomInSession);

    $qUrl = \com\indigloo\Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;


?>

<div id="site-toolbar" class="navbar">

    <div class="row toolbar-row2">
        <div class="span6">
        <h1> <a class="brand" href="/">3mik.com <span class="beta">BETA</span> </a> </h1>

        </div> <!-- span:6 -->
        <div class="span6">
        <div class="floatr">
            <ul class="nav nav-pills">
                <li> <a href="/">Home</a> </li>
                <li class="active"> <a href="<?php echo $qUrl ?>">Back</a> </li>

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
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                About
                <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li> <a href="/site/contact.php">Contact</a> </li>
                    <li> <a href="/site/help.php">Help</a> </li>
                    <li> <a href="#">Copyright</a> </li>
                </ul>
            </li> <!-- About -->
            </ul>
        </div>
        </div> <!-- span:4 -->
    </div> <!-- row1 -->

    <div class="row">
        <?php if(!empty($goverlay)) { echo $goverlay ; } ?>
    </div>

</div> <!-- site toolbar -->




<div id="block-spinner"> </div>
<div id="simple-popup">
    <div id="content"> </div>
    <div class="panel-footer">
        <div class="floatr"><span class="feed-text"> Esc or </span>&nbsp;<a id="simple-popup-close" class="btn btn-primary" href="">close&nbsp;</a> </div>
    </div>
</div> <!-- simple popup -->
<div id="popup-mask"> </div>
