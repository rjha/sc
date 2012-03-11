<?php


// An application is a bundle of 1) web scripts + 2) php libraries + 3) custom dirs
// web scripts will be located via APP_ROOT_DIR
// php libraries are located via APP_LIB_PATH variable and can have multiple entries
// php.ini include_path entries are tried after APP_LIB_PATH entries
// APP_CLASS_LOADER is needed if the application wants to override default class loader.
// APP_WEB_DIR, WEB_TEMPLATE_DIR and WEB_TEMPLATE_COMPILE_DIR are handy shortcuts
// that can also be defined in terms of APP_ROOT_DIR
//
  
$_SERVER['APP_LIB_PATH'] = array('/Users/rjha/code/localo/webgloo/lib','/Users/rjha/code/localo/sc/lib') ;
$_SERVER['APP_CONFIG_PATH'] = '/var/www/apps/sc_config.ini' ;
$_SERVER['APP_CLASS_LOADER'] = '/Users/rjha/code/localo/webgloo/lib/com/indigloo/class_loader.inc' ;
$_SERVER['APP_WEB_DIR'] = '/var/www/vhosts/www.sc.com/htdocs' ;
$_SERVER['WEBGLOO_LIB_ROOT'] = '/Users/rjha/code/localo/webgloo/lib';
 
?>
