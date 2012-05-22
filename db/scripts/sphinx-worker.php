#!/usr/bin/php
<?php 

    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');
    require_once($_SERVER['WEBGLOO_LIB_ROOT']. '/ext/sendgrid-php/SendGrid_loader.php');


    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Configuration as Config;

    set_error_handler('offline_error_handler');
    set_exception_handler('offline_exception_handler');

    $lines = file('/home/rjha/cron/query.log');
    $tokens = array();

    foreach($lines as $line){
        $pieces = explode(" ",$line);
        $entity = $pieces[10];
        $token = $pieces[11];
        if(!in_array($token,$tokens)) {
            array_push($tokens,$token);
        }
    }
    //mail these tokens
    
    $templates = \com\indigloo\sc\html\Mail::getSearchTokens($tokens);
    $text = $templates['text'];
    $html = $templates['html'];
    $subject = " what people searched on 3mik today";
    $from = Config::getInstance()->get_value("default.mail.address");
    $tos = array("jha.rajeev@gmail.com", "sri_saurabh2000@yahoo.com");
    \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$subject,$text,$html);

?>
