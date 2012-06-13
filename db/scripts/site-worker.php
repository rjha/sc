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

    set_error_handler('offline_error_handler');
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

    function process_reset_password($mysqli) {
        $sql = " select email,token from sc_reset_password where flag = 0 order by id limit 50";
        $map = array();
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        foreach($rows as $row) {
            $email = $row['email'];
            if(!in_array($email,$map)){
                //send mail
                $code = \com\indigloo\sc\Mail::sendResetPassword($row['name'],$row['email'], $row['token']);
                if($code > 0 ) {
                    $message = "Error in sending mail. site worker aborting!";
                    throw new Exception($message);
                }
                //set db flag to 1.
                \com\indigloo\sc\mysql\Mail::flipResetPassword($email);
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
            $code = \com\indigloo\sc\Mail::sendActivityMail($name,$email,$feedText,$feedHtml);
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

            if($ownerId != null && ($ownerId != $jobObj->subjectId)) {
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

    //@todo - started@
    //this script is locked via site-worker.sh shell script
    $mysqli = MySQL\Connection::getInstance()->getHandle();
    process_sites($mysqli);
    sleep(1);
    process_groups($mysqli);
    sleep(1);
    process_reset_password($mysqli);
    sleep(1);
    remove_stale_sessions();
    sleep(1);

    //initialize redis connx.
    $redis = \com\indigloo\sc\util\Redis::getInstance()->connection();
    send_notifications($mysqli,$redis);
    $mysqli->close();
    $redis->quit();
    //total time
   ?>
