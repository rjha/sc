<?php

    include ('sc-app.inc');
    //destroy session
    session_start();
    $_SESSION = array();
    session_destroy();
    // delete session cookie
    setcookie("PHPSESSID","",time()-3600,"/");  
    //go back to main site
    header('Location: /');

?>
