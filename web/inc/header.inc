<?php
    //application header
    //classes should be loaded first or we get incomplete class object errors
    // Also, do not output anything in class_loader (apart from errors ) - otherwise you will
    // get session header related warnings. Please keep this order.
    
    
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['APP_WEB_DIR'] . '/inc/session.inc');
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');
    
    use com\indigloo\core\Web  ;
    
    $gWeb = Web::getInstance();
    $gWeb->start();
    
    $gSessionLogin = NULL ;
    $gSiteAddress = 'http://'.$_SERVER['HTTP_HOST'] ;
?>

