<?php

    /*
     * v1.0 12 Dec 2012
     *
     * what this script does?
     * list all versions of an object.
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

    $response = $s3->get_object_headers($bucket,$name);
    $headers = $response->header ;
    $version = $headers["x-amz-version-id"] ;
    printf("object version = %s \n",$version);

?>
