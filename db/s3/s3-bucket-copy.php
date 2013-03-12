<?php

    /*
     * v 0.1 12 March 2013
     *
     * what this script does?
     * copy all objects from source bucket into target bucket
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    error_reporting(-1);

    function read_marker() {
        $fcontent = file_get_contents("./aws.bucket.marker");
        $marker = ($fcontent === false) ? NULL : $fcontent ;
        return $marker;
    }

    function write_marker($marker) {
        file_put_contents("./aws.bucket.marker",$marker);
        $message = sprintf("marker updated to %s ",$marker);
        write_log($message);
    }

    function write_log($message) {
        global $fp_log ;
        // time + message
        $message = sprintf("%s :: %s \n",date(DATE_RFC822),$message); 
        fwrite($fp_log,$message);
    }

    function copy_bucket($s3,$bucket,$size=20) {
        global $target_bucket ;

        $marker = read_marker();
        $list_options = array("max-keys" => $size);
        if(!empty($marker)) {
            $list_options["marker"] = $marker ;
        }

        $response = $s3->list_objects($bucket,$list_options);
        $bor = $response->body ;
        $contents = $bor->Contents ;

        foreach($contents as $content) {
            $fname = $content->Key;
            // copy object
            $mime = NULL ;
            $response = $s3->get_object_metadata($bucket, $fname);
            //get content-type of existing object 
            if($response) {
                $mime = $response["ContentType"] ;
            }
   
            // no mime? treat as arbitrary binary data
            if(empty($mime)) {$mime = "application/octet-stream"; }

            $source = array("bucket" => $bucket, "filename" => $fname);
            $dest = array("bucket" => $target_bucket, "filename" => $fname);

            // caching headers
            // expire after 365 days
            $offset = 3600*24*365;
            $expiresOn = gmdate('D, d M Y H:i:s \G\M\T', time() + $offset);

            $headers = array();
            $headers["Expires"] = $expiresOn ;

            $headers["Cache-Control"] =  "public, max-age=31536000" ;
            $headers["Content-Type"] =  $mime ;

            // This will overwrite existing headers
            $meta = array('acl' => AmazonS3::ACL_PUBLIC, 'headers' => $headers);
            $response = $s3->copy_object($source,$dest,$meta);
            if($response->isOk()){
                $message = sprintf("copied object %s from bucket %s to %s ",$fname,$bucket,$target_bucket);
                write_log($message);
                // update to new marker
                write_marker($content->Key);
                sleep(2);
            }else {
                $message = sprintf("Error copying object %s from bucket %s to %s ",$fname,$bucket,$target_bucket);
                write_log($message);
                // fix this error first
                exit ;
            }

        }

        $flag = $bor->IsTruncated;
        return $flag ;
    }

    // start:script
    $config = parse_ini_file("aws.ini");
    $awsKey = $config["aws.key"];
    $awsSecret = $config["aws.secret"];

    //define:buckets
    $source_bucket = "rjha" ;
    $target_bucket = "test.indigloo" ;

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        
    $s3 = new AmazonS3($options);


    $exists = $s3->if_bucket_exists($source_bucket);
    if(!$exists) { 
        $message = sprintf("source bucket %s does not exists",$source_bucket);
        write_log($message);
        exit ;
    }

    $exists = $s3->if_bucket_exists($target_bucket);
    if(!$exists) { 
        $message = sprintf("target bucket %s does not exists",$target_bucket);
        write_log($message);
        exit ;
    }

    if(!file_exists("./aws.bucket.marker")) {
        // create marker file
        file_put_contents("./aws.bucket.marker","");
    }

    // log file in append mode
    $fp_log = fopen("./aws.bucket.log","a");

    while(1) {
        sleep(1);
        $flag = copy_bucket($s3,$source_bucket);
        $message = sprintf("more objects to fetch? [%s] \n",$flag);
        write_log($message);
        if(strcasecmp($flag, "false") == 0 ) {
            // clean resources
            fclose($fp_log);
            exit ;
        }

    }
    

?>
