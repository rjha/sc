<?php

    /*
     * v1. 01 Oct June 2012
     *
     * what this script does?
     *
     * dump user information as csv file
     *
     * 
     *
     */

    
    include("sc-app.inc");
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL ;

    error_reporting(-1);
    set_exception_handler("offline_exception_handler");

    //no buffer for command line.
    ob_end_flush();

    function write_csv_row($fhandle,$row) {

        if(empty($row["email"])) {
            return ;
        }

        $fname = empty($row["first_name"]) ? "_EMPTY_" : $row["first_name"] ;
        $lname = empty($row["last_name"]) ? "_EMPTY_" : $row["last_name"] ;
        $email = $row["email"];

        $buffer = sprintf("%s,%s,%s \n",$fname,$lname,$email);
        fwrite($fhandle,$buffer) ;
    }
    
    // + start 


    $mysqli = \com\indigloo\mysql\Connection::getInstance()->getHandle();
    $ufile = "3mik-users.csv" ;
    $fhandle = fopen($ufile, "w");
    fwrite($fhandle,"first name,last name,email \n");

    $sql = "select count(id) as total from sc_denorm_user " ;
    $row = MySQL\Helper::fetchRow($mysqli, $sql);
    $total = $row["total"] ;
    $pageSize = 50 ;
    $pages = ceil($total / $pageSize);
    $count = 0 ;

    while($count  <= $pages ){
        $start =  ($count * $pageSize ) + 1 ;
        $end = $start + ($pageSize - 1 ) ;

        $sql = " select * from sc_denorm_user where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        printf("processing user rows between %d and %d \n",$start,$end);

        foreach($rows as $row) {
            write_csv_row($fhandle,$row);
        }


        $count++ ;
    }

    //close resources
    $mysqli->close();
    fclose($fhandle);

?>
