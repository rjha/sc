<?php


// An application is a bundle of 1) web scripts + 2) php libraries + 3) custom dirs
// web scripts will be located via APP_ROOT_DIR
// php libraries are located via APP_LIB_PATH variable and can have multiple entries
// php.ini include_path entries are tried after APP_LIB_PATH entries

  
define("APP_LIB_PATH", "/home/rjha/code/github/webgloo/lib, /home/rjha/code/github/sc/lib") ;
define("APP_CONFIG_PATH", "/var/www/apps/sc_config.ini");
define("APP_CLASS_LOADER", "/home/rjha/code/github/webgloo/lib/com/indigloo/class_loader.inc") ;
define("APP_WEB_DIR", "/var/www/vhosts/mint.3mik.com/htdocs") ;
define("WEBGLOO_LIB_ROOT","/home/rjha/code/github/webgloo/lib");

?>
