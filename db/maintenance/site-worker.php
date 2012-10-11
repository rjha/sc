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

    function send_activity_mail($mysqli,$ownerId,$feedText,$feedHtml) {
        // get owner name and  email
        settype($ownerId, "integer");
        $sql = " select name, email from sc_denorm_user where login_id = %d " ;
        $sql = sprintf($sql,$ownerId);
        $row = MySQL\Helper::fetchRow($mysqli, $sql);

        $name = $row['name'];
        $email = $row['email'];

        if(!empty($email)) {
            // send text and html to email + name.
            $code = WebMail::sendActivityMail($name,$email,$feedText,$feedHtml);
            if($code > 0 ) {
                $message = "Error in sending mail. site worker aborting!";
                throw new Exception($message);
            }
        }

    }

    function remove_job_from_queue($redis,$jobId) {
        $redis->pipeline()
                ->lrem("sc:global:queue:new",1,$jobId)
                ->hdel("sc:global:jobs",$jobId)
                ->uncork();
    }

    function get_pflag($pDataObj, $type) {
        $flag = false ;
        if($type ==  AppConstants::FOLLOW_FEED )
            $flag = $pDataObj->follow ;
        if($type ==  AppConstants::COMMENT_FEED )
            $flag = $pDataObj->comment ;
        if($type ==  AppConstants::BOOKMARK_FEED )
            $flag = $pDataObj->bookmark ;

        return $flag ;
    }

    function send_notifications($mysqli,$redis) {

        // get  new jobIds
        $jobIds = $redis->lrange("sc:global:queue:new", 0,99);
        // feed formatters
        $processor1 = new \com\indigloo\sc\html\feed\PostProcessor();
        $processor2 = new \com\indigloo\sc\html\feed\GraphProcessor();
        $processor3 = new \com\indigloo\sc\html\feed\TextProcessor();

        $templates = array(
                    AppConstants::BOOKMARK_FEED => "/fragments/feed/email/post.tmpl",
                    AppConstants::COMMENT_FEED => "/fragments/feed/email/comment.tmpl",
                    AppConstants::POST_FEED => "/fragments/feed/email/post.tmpl",
                    AppConstants::FOLLOW_FEED => "/fragments/feed/email/vanilla.tmpl");

        $mapHtmlProcessor = array(AppConstants::FOLLOW_FEED => $processor2,
                                AppConstants::COMMENT_FEED => $processor1,
                                AppConstants::BOOKMARK_FEED => $processor1,
                                AppConstants::POST_FEED => $processor1);

        $mapTextProcessor = array(AppConstants::FOLLOW_FEED => $processor3,
                                AppConstants::COMMENT_FEED => $processor3,
                                AppConstants::BOOKMARK_FEED => $processor3,
                                AppConstants::POST_FEED => $processor3);

        $feedText = NULL ;
        $feedHtml = NULL ;
        $processor = NULL ;

        foreach($jobIds as $jobId) {
            //fetch the payload now
            $jobJson = $redis->hget("sc:global:jobs", $jobId);
            //process job payload now.
            $jobObj = json_decode($jobJson);
            $ownerId = NULL ;

            switch($jobObj->type) {
                case AppConstants::FOLLOW_FEED :
                    $ownerId = $jobObj->objectId ;
                    break ;
                case AppConstants::BOOKMARK_FEED :
                case AppConstants::COMMENT_FEED :
                    $ownerId = $jobObj->ownerId ;
                    break ;
                default:
                    $ownerId = NULL ;
            }

            //get preferences on ownerId
            $preferenceDao = new \com\indigloo\sc\dao\Preference();
            $pDataObj = $preferenceDao->get($ownerId);
            $pflag = get_pflag($pDataObj,$jobObj->type);
            
            if($ownerId != null && $pflag && ($ownerId != $jobObj->subjectId)) {
                //send mail to owner.
                // html content of mail
                $processor = $mapHtmlProcessor[$jobObj->type];
                $feedHtml = $processor->process($jobObj,$templates);
                //text content of mail.
                $processor = $mapTextProcessor[$jobObj->type];
                $feedText = $processor->process($jobObj);
                send_activity_mail($mysqli,$ownerId,$feedText,$feedHtml);
                remove_job_from_queue($redis,$jobId);

            }else {
                //nothing to do.
                remove_job_from_queue($redis,$jobId);
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


    //initialize redis connx.
    $redis = \com\indigloo\connection\Redis::getInstance()->connection();
    send_notifications($mysqli,$redis);

    //release resources
    $mysqli->close();
    $redis->quit(); 

   ?>
