<?php

    include("sc-app.inc");
    include(APP_CLASS_LOADER);

    use \com\indigloo\Util as Util ;

    error_reporting(-1);

    function check_input($number,$qparams,$pageNo,$start,$direction) {
        global $pageSize ;
        $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
        $paginator->setBaseConvert(false);

        if($pageNo != $paginator->getPageNo()) {
            printf("Test # %d failed \n",$number);

        }

        $params = $paginator->getDBParams();
        if($params["start"] != $start) {
            printf("Test # %d failed \n",$number);
        }

        if(strcmp($params["direction"],$direction) != 0 ) {
            printf("Test # %d failed \n",$number);
        }


    }

    // Rules
    // 1) empty, negative and garbage gpage is converted to page #1
    // 2) there is no relationship between gpage variable and gpa/gpb
    // 3) Default DB params are before + 1 
    //
    // 4) if both gpa and gpb are present then gpb takes precedence
    // 5) garbage gpa/gpb is turned into zero
    // 6) empty gpa/gpb is neglected 

    //fixed
    $pageSize = 10 ;
    $number = 1 ;
    //vary input
    $qparams = NULL ;
    check_input($number,$qparams,1,1,"before");
    $number++;

    #2
    $qparams = array() ;
    check_input($number,$qparams,1,1,"before");
    $number++;

    #3
    $qparams = array("gpage"=> "") ;
    check_input($number,$qparams,1,1,"before");
    $number++;

    #4
    $qparams = array("gpage"=> "$#@") ;
    check_input($number,$qparams,1,1,"before");
    $number++;

    #5
    $qparams = array("gpage"=> "-123") ;
    check_input($number,$qparams,1,1,"before");
    $number++;

    #6
    $qparams = array("gpage"=> "2", "gpa" => "0") ;
    check_input($number,$qparams,2,0,"after");
    $number++;

    #7
    $qparams = array("gpage"=> "2", "gpa" => "$#@") ;
    check_input($number,$qparams,2,0,"after");
    $number++;

    #8
    $qparams = array("gpage"=> "2", "gpa" => 2, "gpb" => 2) ;
    check_input($number,$qparams,2,2,"before");
    $number++;

    #9
    $qparams = array("gpage"=> "2", "gpa" => 2, "gpb" => "0") ;
    check_input($number,$qparams,2,0,"before");
    $number++;

    #10
    $qparams = array("gpage"=> "2", "gpa" => 2, "gpb" => "@#@@@$") ;
    check_input($number,$qparams,2,0,"before");
    $number++;

    #11
    $qparams = array("gpage"=> "2", "gpa" => 2, "gpb" => "  ") ;
    check_input($number,$qparams,2,2,"after");
    $number++;

    printf("  *** Testing over *** \n\n");
?>
