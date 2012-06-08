<?php

    include ('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(APP_WEB_DIR . '/inc/session.inc');
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    //destroy session
    $_SESSION = array();
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }


    //redirect to a _session free page
    //redirecting to HOME will start a new session
    header('Location: /site/thanks.php');

?>
