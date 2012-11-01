<?php


    function process_line($line) {
        global $bucket ;
        global $fhandle ;

        $line = trim($line) ;
        if(empty($line)) { return ; }

        //break on pipe
        $tokens = explode("|",$line);

        if(empty($tokens)) {
            return ;
        } 

        $first = $tokens[0] ;
        $first = trim($first);
        //break on space now
        $pieces = explode(" ",$first);
        $email = NULL ;

        $size = sizeof($pieces) ;
        switch($size) {
            case 1 :
                $email = $pieces[0] ;
                break ;
            case 2: 
                $email = $pieces[1] ;
                break ;
            default :
                printf("explode trick failed for = %s \n",$line);
                printf("exploded array is \n");
                print_r($pieces);
                exit ;
        }

        if(empty($email)) {
            printf("explode trick failed for = %s \n",$line);
            exit ;
        }

        //already seen?
        if(!empty($email) 
            && (strcmp($email,"__EMAIL__") != 0 ) 
            && (!in_array($email,$bucket))) {
            //process
            $name = isset($tokens[1]) ? $tokens[1] : "" ;
            $title = isset($tokens[2]) ? $tokens[2] : "" ;
            $company = isset($tokens[3]) ? $tokens[3] : "" ;

            $data = sprintf("%s | %s | %s | %s \n",$email,$name,$title, $company);
            fwrite($fhandle,$data);
            //update bucket
            array_push($bucket,$email);
        }

        return ;
    }

    $bucket = array();
    $fhandle = fopen("uniq.email","w");

    //files from current directory
    $all_files = scandir(__DIR__);
    $ignore = array(".", "..","uniq.php", "uniq.email");
    $ifiles = array_diff($all_files,$ignore);

    foreach($ifiles as $ifile ){
        //get lines from file
        $lines = file($ifile);
        foreach($lines as $line) {
            process_line($line);
        }
    }


    //release resources 
    fclose($fhandle);
?>
