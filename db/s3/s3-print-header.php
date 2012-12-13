<?php

    /*
     * v1. 22 June 2012
     *
     * what this script does?
     *
     * print headers for an object in s3 bucket 
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

    $response = $s3->get_object_headers($bucket, $name);
    print_r($response);

?>
