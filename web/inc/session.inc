<?php

//default session management
ini_set('session_use_cookies',1);
//Defaults to 1 (enabled) since PHP 5.3.0
//no passing of sessionID in URL
ini_set('session.use_only_cookies',1);
session_start();

?>
