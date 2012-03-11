<?php

ini_set('session_use_cookies',1);
//Defaults to 1 (enabled) since PHP 5.3.0
//no passing of sessionID in URL
ini_set('session.use_only_cookies',1);

if(isset($_SESSION) && isset($_SESSION['MAGIC_SESSION'])) {
    // session started : do nothing
} else {
    // start session, push magic variable in session
    session_start();
    $_SESSION['MAGIC_SESSION'] = 'YES' ;
}

?>