#!/usr/bin/php
<?php

    /*  
        purpose
            - script to load all sc_activity data into redis store

        before running this script
            - shut down the site
            - turn off site worker cron job (put an exit at top)
            - redis-cli : issue command flushdb - clean old data from redis store

        after running this script
            - turn ON op_bit of sc_activity rows
        @warnings 
            - make sure we are not sending mails 
                sendgrid.mail.mode="development"
            - this script will process all activities
                irrespective of op_bit flag.
            
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


        $activityDao = new \com\indigloo\sc\dao\Activity();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_activity where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {
                $activityDao->pushToRedis($row);
            }
            
            printf("processed rows between %s and %s \n",$start,$end);
            flush();
            sleep(1);
            $count++ ;

        }
    }

    ob_end_clean();

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    load_in_redis($mysqli);
    //close resources
    $mysqli->close();
?>
