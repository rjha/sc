<?php
    //sc/qa/show.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    
    use com\indigloo\Util as Util;
    use com\indigloo\Url as Url;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
     
    $postId = Url::getQueryParam("id");

    //Add permanent redirect
    $redirectUrl = "/item/".PseudoId::encode($postId) ;
    header( "HTTP/1.1 301 Moved Permanently" ); 
    header( "Location: ".$redirectUrl );   
    exit ;
?>
