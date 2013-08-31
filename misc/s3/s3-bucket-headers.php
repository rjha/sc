<?php

    /*
     * @author rajeev jha
     * @version 0.1 
     * @date 12 March 2013
     *
     * what this script does?
     * update caching headers of all objects in the bucket to one year in future. 
     * This is useful for serving images from an s3 bucket.
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    error_reporting(-1);

    function read_marker() {
        global $marker_file;
        $fcontent = file_get_contents($marker_file);
        $marker = ($fcontent === false) ? NULL : $fcontent ;
        return $marker;
    }

    function write_marker($marker) {
        global $marker_file;
        file_put_contents($marker_file,$marker);
        $message = sprintf("marker updated to %s ",$marker);
        write_log($message);
    }

    function write_log($message) {
        global $fp_log ;
        // time + message
        $message = sprintf("%s :: %s \n",date(DATE_RFC822),$message); 
        fwrite($fp_log,$message);
    }

    function update_object_headers($s3,$size=20) {
        global $source_bucket ;

        $marker = read_marker();
        $list_options = array("max-keys" => $size);
        if(!empty($marker)) {
            $list_options["marker"] = $marker ;
        }

        $response = $s3->list_objects($source_bucket,$list_options);
        $bor = $response->body ;
        $contents = $bor->Contents ;

        foreach($contents as $content) {
            $fname = $content->Key;
            // copy object
            $mime = NULL ;
            $response = $s3->get_object_metadata($source_bucket, $fname);
            //get content-type of existing object 
            if($response) {
                $mime = $response["ContentType"] ;
            }
   
            // no mime? treat as arbitrary binary data
            if(empty($mime)) {$mime = "application/octet-stream"; }

            $source = array("bucket" => $source_bucket, "filename" => $fname);
            $dest = array("bucket" => $source_bucket, "filename" => $fname);

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
                $message = sprintf("updated caching headers for object %s ",$fname);
                write_log($message);
                // update to new marker
                write_marker($content->Key);
                sleep(2);
            }else {
                $message = sprintf("Error updating caching headers for object %s ",$fname);
                write_log($message);
                // fix this error first
                exit ;
            }

        }

        $flag = $bor->IsTruncated;
        return $flag ;
    }

    // start:script
    // define:variables
    $config = parse_ini_file("aws.ini");
    $awsKey = $config["aws.key"];
    $awsSecret = $config["aws.secret"];

    $source_bucket = "media1.3mik.com" ;
    $marker_file = "./aws.bucket.headers.marker" ;
    $log_file = "./aws.bucket.headers.log" ;
    
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

    if(!file_exists($marker_file)) {
        // create new marker file
        file_put_contents($marker_file,"");
    }

    $fp_log = fopen($log_file,"a");

    while(1) {
        sleep(1);
        $flag = update_object_headers($s3);
        $message = sprintf("more objects to fetch? [%s] \n",$flag);
        write_log($message);
        if(strcasecmp($flag, "false") == 0 ) {
            // clean resources
            fclose($fp_log);
            exit ;
        }

    }
    

?>
