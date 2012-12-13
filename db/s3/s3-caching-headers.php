<?php

    /*
     * v1. 22 June 2012
     *
     * what this script does?
     *
     * add caching  headers to an existing object
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
   
    error_reporting(-1);
    $config = parse_ini_file("aws.ini");
    $awsKey = $config["aws.key"];
    $awsSecret = $config["aws.secret"];

    $bucket = "test.indigloo" ;
    $name = "garbage_bin_wallpaper.jpg" ;

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        
    $s3 = new AmazonS3($options);
    
    $exists = $s3->if_bucket_exists($bucket);
    if(!$exists) {
       printf("S3 bucket %s does not exists \n" , $bucket);
       exit ;
    }

    $mime = NULL ;

    $response = $s3->get_object_metadata($bucket, $name);
    //get content-type of existing object 
    if($response) {
        $mime = $response["ContentType"] ;
    }
   
    if(empty($mime)) {
        printf("No mime found for object \n");
        exit ;
    }

    $source = array("bucket" => $bucket, "filename" => $name);
    $dest = array("bucket" => $bucket, "filename" => $name);

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
        printf("s3 object copy done \n");
    }else {
        printf("Error :: s3 object copy \n");
        exit ;
    }

?>