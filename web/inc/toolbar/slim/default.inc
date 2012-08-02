<?php

    $gWeb = \com\indigloo\core\Web::getInstance();
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
                <li> <a href="/site/help.php">Help</a> </li>
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
