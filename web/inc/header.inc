<?php
    //application header
    //classes should be loaded first or we get incomplete class object errors
    // Also, do not output anything in class_loader (apart from errors ) - otherwise you will
    // get session header related warnings. Please keep this order.


    include(APP_CLASS_LOADER);

    use com\indigloo\core\Web  ;
    use com\indigloo\Configuration as Config  ;

    $session_backend = Config::getInstance()->get_value("session.backend");
    $session_backend = empty($session_backend) ? "default" :  strtolower($session_backend);

    switch($session_backend) {
        case "redis" :
            include(APP_WEB_DIR . '/inc/redis-session.inc');
        break;
        case "mysql" :
            include(APP_WEB_DIR . '/inc/mysql-session.inc');
        break ;
        default:
            include(APP_WEB_DIR . '/inc/session.inc');
    }

    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    $gWeb = Web::getInstance();
    $gWeb->start();
    
?>
