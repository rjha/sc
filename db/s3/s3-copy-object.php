<?php

    /*
     * v1. 22 June 2012
     *
     * what this script does?
     * copy object from source bucket into target bucket
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    error_reporting(-1);

    $config = parse_ini_file("aws.ini");
    $awsKey = $config["aws.key"];
    $awsSecret = $config["aws.secret"];

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        
    $s3 = new AmazonS3($options);
    $source = "test.indigloo" ;
    $target = "rjha" ;
    $name ="polaroid.swf" ;


    $exists = $s3->if_bucket_exists($source);
    if(!$exists) { 
        $message = sprintf("source bucket %s does not exists",$source);
        printf("%s \n",$message);
        exit ;
    }

    $exists = $s3->if_bucket_exists($target);
    if(!$exists) { 
        $message = sprintf("target bucket %s does not exists",$source);
        printf("%s \n",$message);
        exit ;
    }


    $source = array("bucket" => $source, "filename" => $name);
    $dest = array("bucket" => $target, "filename" => $name);
    $response = $s3->copy_object($source,$dest);

    if($response->isOk()){ printf("s3 object copy done \n"); }
    else { printf("Error :: s3 object copy \n"); }



?>
