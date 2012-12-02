<?php


    /* 
     * -----------------------------------------------------
     * script to upload a local folder into rackspace 
     * pseudo_directory hierarchy inside a container 
     *
     * -------------------------------------------------------
     *
     */

    error_reporting(-1);

    function do_upload($ch2,$auth,$fname) {

        $headers = array();
        $grab = array("X-Auth-Token");
        $host = $auth["X-Storage-Url"];

        foreach($auth as $name => $value) {
            if(in_array($name,$grab)) {
                array_push($headers, "$name: $value");
            }
        }

        // Content-Type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fname);
        finfo_close($finfo);
        $etag = md5_file($fname);

        array_push($headers, "Content-Type: $mime");
        array_push($headers, "ETag: $etag");

        $fp = fopen($fname, "r");
        $fsize = filesize($fname);
        // asset is container name
        // upload in a pseudo_dir structure
        // <storage-url>/asset/css/...
        //
        $url = $host. "/asset/".$fname ;
        printf("HTTP PUT %s  to => %s \n",$fname,$url); 


        $options = array(
            CURLOPT_TIMEOUT => 60 ,
            CURLOPT_RETURNTRANSFER => 1 ,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $url,
            CURLOPT_VERBOSE => false,
            CURLOPT_HEADER => 1,
            CURLOPT_PUT => 1 ,
            CURLOPT_INFILE => $fp,
            CURLOPT_INFILESIZE => $fsize);



        // print_r($headers); 
        // Do a PUT operation

        curl_setopt_array($ch2, $options);
        $response = curl_exec ($ch2);
    }


    function do_auth() {

        $ch = curl_init();

        $host = "https://identity.api.rackspacecloud.com/v1.0" ;
        $apiKey = "xxxxxxx" ;
        $user = "yyyyyyyyy" ;

        $headers = array(
            "X-Auth-Key: $apiKey " ,
            "X-Auth-User: $user");

        $options = array(
            CURLOPT_TIMEOUT => 60 ,
            CURLOPT_RETURNTRANSFER => 1 ,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $host,
            CURLOPT_VERBOSE => false,
            CURLOPT_HEADER => 1);

        curl_setopt_array($ch, $options);
        $response = curl_exec ($ch);
        curl_close($ch);

        list($headers, $body) = explode("\r\n\r\n", $response, 2);

        $lines = explode("\n",$headers);
        $auth = array();
        $grab = array("X-Storage-Token", "X-Storage-Url","X-Auth-Token");

        foreach($lines as $line ) {
            $parts = explode(" ",$line);
            $name = $parts[0] ;
            $name = trim($name,": ");

            if(in_array($name,$grab)) {
                $auth[$name] = trim($parts[1]);
            }

        } 

        return $auth ;

    }

    $auth = do_auth();
    printf(" \n **** parsed auth headers for PUT **** \n");
    print_r($auth);

    $ch2 = curl_init();
    // get all files in css dir
    $files = array();

    // load everything from local css folder into
    // <storage-url>/asset/css/local-file-path
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('css')) as $path) {
        $filename = sprintf("%s",$path);
        $pos = strrpos($filename,"/");
        if($pos !== false) {
            $last = substr($filename,$pos+1);
            if($last == '.' || $last == '..') {
                printf(" ignore file :: %s \n",$filename); 
            }else {
                array_push($files,$filename);
            }
        }
    }

    foreach($files as $file){
        do_upload($ch2,$auth,$file);
    }

    curl_close($ch2);


?>
