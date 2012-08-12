<?php
    //application header
    //classes should be loaded first or we get incomplete class object errors
    // Also, do not output anything in class_loader (apart from errors ) - otherwise you will
    // get session header related warnings. Please keep this order.


    include(APP_CLASS_LOADER);

    use com\indigloo\core\Web  ;
    use com\indigloo\Configuration as Config  ;

    $sessionBackend = Config::getInstance()->get_value("session.backend");

    if(strcmp($sessionBackend,"mysql") == 0 ) {
        include(APP_WEB_DIR . '/inc/mysql-session.inc');
    }else{
        include(APP_WEB_DIR . '/inc/session.inc');
    }

    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    $gWeb = Web::getInstance();
    $gWeb->start();
    
?>
