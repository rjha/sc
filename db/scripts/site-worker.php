#!/usr/bin/php
<?php 

    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Configuration as Config;

    set_error_handler('offline_error_handler');
    set_exception_handler('offline_exception_handler');

    function process_sites($mysqli) {
        //process sites
        $sql = " select post_id from sc_site_tracker where site_flag = 0 order by id desc limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $siteDao = new \com\indigloo\sc\dao\Site();

        foreach($rows as $row) {
            $postId = $row["post_id"];
            $siteDao->process($postId);
            sleep(1);
        }
    }

    function process_groups($mysqli) {
        //process groups 
        $sql = " select post_id from sc_site_tracker where group_flag = 0 order by id desc limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $groupDao = new \com\indigloo\sc\dao\Group();

        foreach($rows as $row) {
            $postId = $row["post_id"];
            $groupDao->process($postId);
        }
    }

    function process_reset_password($mysqli) {
        $sql = " select email,token from sc_reset_password where flag = 0 order by id limit 10";
        $map = array(); 
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $mailDao = new \com\indigloo\sc\dao\Mail();

        foreach($rows as $row) {
            $email = $row['email'];
            if(!in_array($email,$map)){
                $mailDao->processResetPassword($row['email'], $row['token']);
                array_push($map,$email);
            }
        }
    }

    function remove_stale_sessions(){
        //clean sessions inactive for half an hour
        $mysql_session = new \com\indigloo\core\MySQLSession();
        $mysql_session->open(null,null);
        //30 minutes * 60 seconds
        $mysql_session->gc(1800);
        $mysql_session->close();
    }

    //this script is locked via site-worker.sh shell script
    $mysqli = MySQL\Connection::getInstance()->getHandle();
    process_sites($mysqli);
    sleep(1);
    process_groups($mysqli);
    sleep(1);
    process_reset_password($mysqli);
    sleep(1);
    remove_stale_sessions();

   ?>
