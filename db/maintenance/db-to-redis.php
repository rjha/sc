#!/usr/bin/php
<?php

    /*  

        * 
        Before running the script
        -----------------------------
        + sc_bookmark, sc_post, sc_comment, sc_follow table should exist
        
        After running the script
        -------------------------------
        + remove global job queue and jobs.
        
        del  sc:global:queue:new 
        del  sc:global:jobs
        del sc:global:nextJobId

        + verify using
        
        exists sc:global:nextJobId
        exists sc:global:jobs
        exists sc:global:queue:new

        + turn on cron job
        @todo- do we need to reset sc:global:nextJobId ?


        purpose
            - script to load all sc_activity data into redis store

        before running this script
            - shut down the site
            - turn off site worker cron job (put an exit at top)
            - redis-cli : issue command flushdb - clean old data from redis store

        after running this script
            - turn ON op_bit of sc_activity rows
        precautions
            - make sure we are not sending mails 
            




    */

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\Util ;

    set_exception_handler('offline_exception_handler');

    function load_in_redis($mysqli) {
        
        $sql = "select max(id) as total from sc_activity " ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);

        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;


        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_activity where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {
                $activityDao->pushToRedis($row);
            }
            
            sleep(1);
            $count++ ;

        }
    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    load_in_redis($mysqli);
    //close resources
    $mysqli->close();
?>
