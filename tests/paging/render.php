<?php
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\sc\Util as AppUtil ;
    use \com\indigloo\sc\redis as redis;
    use \com\indigloo\mysql as MySQL;

    set_error_handler('webgloo_error_handler');
    set_exception_handler('offline_exception_handler');

    $pageSize = 10 ;
    $baseURI = "/home" ;
    $totals  = array(0,7,10,17,20,24) ;
    foreach($totals as $total) {
        render_links($total);
    }

    function render_links($total) {
        global $baseURI ;
        global $pageSize ;
        for($j = 1; $j <= 10 ;$j++) {
            $qparams = array("gpage" => $j , "gpa" => $j*$pageSize );
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);
            $paginator->setMaxPageNo(4);

            $start = ($j -1 ) * $pageSize ;
            $end = $start + $pageSize ;
            $end = ($end > $total) ? $total : $end ;
            $gNumRecords = ($end - $start);
            
            printf(" \n\n Total (%d) page-%d \t start-%d end-%d \t gNumRecords %d \n ",$total,$j,$start,$end,$gNumRecords);
            $paginator->render($baseURI,$start,$end,$gNumRecords);
            printf("  \n ---------------- \n"); 
            if(!$paginator->hasNext($gNumRecords)) { break ; }
        }

    }

?>
