<?php

    /*
     * v1.0 12 Dec 2012
     *
     * what this script does?
     * enable versioning on a bucket
     *
     *
     * @issue with sdk-1.5.7 : the script will complain about bad SSL certificate,
     * whether you use in-built cert  (true) or use one from your system (false)
     *
     * Right now the only way to make that work is to shut off SSL in sdk-1.5.7/service/s3.class.php
     * file at @line 552.
     *
     * 552 $scheme = $this->use_ssl ? 'https://' : 'http://';
     * 553 $scheme =  'http://';
     * 
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    include("sc-app.inc");
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\Configuration as Config ;

    error_reporting(-1);
    set_exception_handler("offline_exception_handler");

    //no buffer for command line.
    ob_end_flush();

    // Instantiate the AmazonS3 class
    $awsKey = Config::getInstance()->get_value("aws.key");
    $awsSecret = Config::getInstance()->get_value("aws.secret");

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        
    $s3 = new AmazonS3($options);
    $bucket = "test.indigloo" ;
    $s3->enable_versioning($bucket);

?>
