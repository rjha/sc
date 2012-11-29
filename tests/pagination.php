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
    $total = 47 ;

    for($j = 1; $j <= 10 ;$j++) {
        $qparams = array("gpage" => $j , "gpa" => $j*$pageSize );
        $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
        $paginator->setBaseConvert(false);
        $paginator->setMaxPageNo(4);

        $start = ($j -1 ) * $pageSize ;
        $end = $start + $pageSize ;
        $end = ($end > $total) ? $total : $end ;
        $gNumRecords = ($end - $start);


        
        printf(" \n\n -- page %d -- \n ",$j);
        printf(" start %d - end %d - records %d \n ",$start,$end,$gNumRecords);
        $paginator->render($baseURI,$start,$end,$gNumRecords);
        if(!$paginator->hasNext($gNumRecords)) { break ; }
    }

?>

