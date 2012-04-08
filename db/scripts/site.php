#!/usr/bin/php
<?php 

	include('sc-app.inc');
	include($_SERVER['APP_CLASS_LOADER']);

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\mysql as MySQL;

    function offline_error_handler($errorno,$errorstr,$file,$line) {
        switch($errorno) { 
            case E_STRICT :
                return true;
            case E_NOTICE :
            case E_USER_NOTICE :
                Logger::getInstance()->error(" $file :: $line :: $errorstr");
                break ;
            default:
                Logger::getInstance()->error("offline error handler...");
                $message = sprintf("file %s - line - %s :: %s \n",$file,$line,$errorstr); 
                Logger::getInstance()->error($message);
                Logger::getInstance()->backtrace();
                exit(1) ;
        }
    }

    set_error_handler('offline_error_handler');

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    $sql = " select post_id from sc_site_tracker where flag = 0 order by id limit 50";
    $rows = MySQL\Helper::fetchRows($mysqli, $sql);
    $siteDao = new \com\indigloo\sc\dao\Site();

    foreach($rows as $row) {
        $postId = $row["post_id"];
        $siteDao->process($postId);
        sleep(1);
    }

?>
