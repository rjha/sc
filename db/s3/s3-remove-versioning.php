<?php

    /*
     * v1.0 12 Dec 2012
     *
     * what this script does?
     * enable versioning on a bucket
     *
     *
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    error_reporting(-1);

    $config = parse_ini_file("aws.ini");
    $awsKey = $config["aws.key"];
    $awsSecret = $config["aws.secret"];
    $bucket = "test.indigloo" ;

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        

    $s3 = new AmazonS3($options);
    $s3->disable_versioning($bucket);
    
?>
