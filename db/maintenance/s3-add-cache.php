<?php

    /*
     * @issue with sdk-1.5.7 : the script will complain about bad SSL certificate,
     * whether you use in-built cert  (true) or use one from your system (false)
     *
     * Right now the only way to make that work is to shut off SSL in sdk-1.5.7/service/s3.class.php
     * file at @line 552.
     *
     * 552 $scheme = $this->use_ssl ? 'https://' : 'http://';
     * 553 $scheme =  'http://';
     * 
     * $sudo php s3-add-cache.php >> s3.log 2>&1
     * and then you can tail the log file.
     *
     * 1)
     * we added support for caching headers on amazon s3 objects on 18 Mar, 2012
     * @see https://github.com/rjha/sc/commit/918bbcaac2bd066451d74c96aa1f3a9d663bf3f5
     * so all objects uploaded after 20 march 2012 should hace caching headers.
     *
     * 2) caching RFC does not recommend setting expires on for more than one year in future.
     * that is why we are setting expire date 1 year in future. We need to check browser caching 
     * again on 18 March, 2013.
     *
     * 3) How to touch all objects again? Right now we are using aws-sdk and copy_object. 
     * we may want to use a ready made solution in future.
     *
     * 4) @todo Backup of media1.3mik.com bucket is essential.
     *
     *
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    include("sc-app.inc");
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\Configuration as Config ;

    error_reporting(-1);
    set_error_handler("offline_error_handler");
    set_exception_handler("offline_exception_handler");

    //no buffer for command line.
    ob_end_flush();

    function print_headers($s3,$bucket,$name) {
        $response = $s3->get_object_headers($bucket, $name);
        print_r($response);
    }

    function add_caching_headers($s3,$bucket,$name,$itemId) {
        $source = array("bucket" => $bucket, "filename" => $name);
        $dest = array("bucket" => $bucket, "filename" => $name);

        //caching headers
        $offset = 3600*24*365;
        $expiresOn = gmdate('D, d M Y H:i:s \G\M\T', time() + $offset);
        $headers = array('Expires' => $expiresOn, 'Cache-Control' => 'public, max-age=31536000');
        $meta = array('acl' => AmazonS3::ACL_PUBLIC, 'headers' => $headers);

        $response = $s3->copy_object($source,$dest,$meta);
        if($response->isOk()){
            printf("copy :: %d :: http://%s/%s \n",$itemId,$bucket,$name);

        }else {
            printf("Error :: copy :: %s \n",$name);
        }
    }

    // Instantiate the AmazonS3 class
    $awsKey = Config::getInstance()->get_value("aws.key");
    $awsSecret = Config::getInstance()->get_value("aws.secret");

    $options = array(
        "key" => $awsKey , 
        "secret" => $awsSecret, 
        "default_cache_config" => '', 
        "certificate_authority" => true);
        
    $s3 = new AmazonS3($options);
    $bucket = "media1.3mik.com" ;


    $exists = $s3->if_bucket_exists($bucket);
    if(!$exists) {
        trigger_error("S3 bucket does not exists \n" , E_USER_ERROR);
    }else {
        printf("bucket %s found \n" ,$bucket);

    }

    //get image paths from DB
    $mysqli = \com\indigloo\mysql\Connection::getInstance()->getHandle();
    for($page = 1 ; $page <= 40; $page++) {
        $offset = (($page-1) * 50) ;
        $sql = "select id, bucket,stored_name,created_on  from sc_media where created_on < '2012-03-21' order by id limit %d,50";
        $sql = sprintf($sql,$offset);
        $rows = \com\indigloo\mysql\Helper::fetchRows($mysqli,$sql);

        foreach($rows as $row) {
            $name = $row["stored_name"];
            add_caching_headers($s3,$bucket,$name,$row['id']);
            sleep(1);
        }
    }

?>
