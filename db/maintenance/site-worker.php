#!/usr/bin/php
<?php

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Util as Util;

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\sc\Mail as WebMail ;

    set_exception_handler('offline_exception_handler');

    function process_sites($mysqli) {
        //process sites
        $sql = " select post_id from sc_site_tracker where site_flag = 0 order by id desc limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        //halt for error.
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

    function process_mail_queue($mysqli) {

        $sql = " select * from sc_mail_queue where flag = 0 order by id limit 50";
        $map = array();
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        foreach($rows as $row) {
            $email = $row["email"];
            $source = $row["source"];
            settype($source,"integer");

            $khash = md5($email.$source);
            $name = $row["name"];

            if(!in_array($khash,$map)){
                // send mail
                // assume error 
                $code = 1 ;

                switch($source) {
                    case AppConstants::RESET_PASSWORD_MAIL :
                        $code = WebMail::sendResetPassword($name,$email, $row["token"]);
                        \com\indigloo\sc\mysql\Mail::toggle($email);
                        array_push($map,$khash);
                    break ;
                    case AppConstants::NEW_ACCOUNT_MAIL :
                        $code = WebMail::newAccountMail($name,$email);
                    break ;
                }
                
                if($code > 0 ) {
                    $message = sprintf("code %s - error sending mail. aborting!",$code);
                    throw new Exception($message);
                }

                //mail went 
                \com\indigloo\sc\mysql\Mail::toggle($email);
                array_push($map,$khash);
                
            }
        } //:loop

        //delete old mails in queue 
        $sql2 = " delete from sc_mail_queue where flag = 1 and created_on < (now() - interval 1 DAY)";
        MySQL\Helper::executeSQL($mysqli, $sql2);

    }

    function remove_mysql_sessions(){
        //clean sessions inactive for half an hour
        $mysql_session = new \com\indigloo\core\MySQLSession();
        $mysql_session->open(null,null);
        //7 days * 24 HR /Day * 3600 seconds/Hour
        $lifetime = Config::getInstance()->get_value("session.lifetime",3600);
        $mysql_session->gc($lifetime);
        $mysql_session->close();
    }

    function process_activities($mysqli,$mode=0) {
        //process activities data 
        $sql = " select * from sc_activity where op_bit = 0 order by id desc limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
        $sql2 = " update sc_activity set op_bit = 1 where id = %d " ;

        foreach($rows as $row) {
            try{
                $feed = $activityDao->pushToRedis($row);
                // comment out in DEV mode
                if($mode == 1){
                    $activityDao->sendMail($row,$feed,$preferenceObj);
                }else {
                    $message = sprintf("\n\n activity_id = %s \n mail = %s \n\n",$row["id"],$feed);
                    Logger::getInstance()->info($message);
                }

                //flip the op_bit for this activity
                $sql2 = sprintf($sql2,$row["id"]);
                MySQL\Helper::executeSQL($mysqli, $sql2);

            } catch(\Exception $ex) {
                Logger::getInstance()->error($ex->getMessage());
            }
        }
    }
    

   
    //this script is locked via site-worker.sh shell script
    $mysqli = MySQL\Connection::getInstance()->getHandle();
   
    process_sites($mysqli);
    sleep(1);
    process_groups($mysqli);
    sleep(1); 
    process_mail_queue($mysqli);
    sleep(1);

    $session_backend = Config::getInstance()->get_value("session.backend");
    $session_backend = empty($session_backend) ? "default" :  strtolower($session_backend);
    if(strcmp($session_backend,"mysql") == 0 ) {
        remove_mysql_sessions();
    }

    sleep(1);

    //mode = 1 for sending actual mails
    process_activities($mysqli);

    //release resources
    $mysqli->close();

    // write compiled template dir location
    // we want to chown ownership of any compiled
    // template dir created by this script (that is run as sudo)
    // to www-data:www-data
    // otherwise web code (run as www-data) will not be able to open those
    // compiled templates
    // The actual chown operation is done by site-worker.sh script
    $template_dir = trim(APP_WEB_DIR."/compiled") ;
    file_put_contents("tmpl.location",$template_dir);
    //delay - let file content be there!
    sleep(2);

   ?>
