<?php

    /*
     * v1. 22 June 2012
     *
     * what this script does?
     *
     * This script allows you to modify  headers for existing sc_media objects in Amazon S3
     * This script can be used to alter caching headers or mime types.
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
     * $sudo php s3-add-headers.php >> s3.log 2>&1
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
     * 4) script run till sc_media.id = 5677 (18-june-2012) on bucket bkp1.3mik.com
     *   next backup should start from here.
     *
     *
     */

    require_once "sdk-1.5.7/sdk.class.php";
    include("sc-app.inc");
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL ;

    error_reporting(-1);
    set_error_handler("offline_error_handler");
    set_exception_handler("offline_exception_handler");

    //no buffer for command line.
    ob_end_flush();

    function print_object_headers($s3,$bucket,$name) {
        $response = $s3->get_object_headers($bucket, $name);
        print_r($response);
    }

    function add_object_headers($s3,$bucket,$name,$rowId,$mime) {
        $source = array("bucket" => $bucket, "filename" => $name);
        $dest = array("bucket" => $bucket, "filename" => $name);

        //caching headers
        $offset = 3600*24*365;
        $expiresOn = gmdate('D, d M Y H:i:s \G\M\T', time() + $offset);

        $headers = array();
        $headers["Expires"] = $expiresOn ;
        $headers["Cache-Control"] =  "public, max-age=31536000" ;
        $headers["Content-Type"] =  $mime ;

        $meta = array('acl' => AmazonS3::ACL_PUBLIC, 'headers' => $headers);

        $response = $s3->copy_object($source,$dest,$meta);
        if($response->isOk()){
            printf("s3 object copy :: %d :: http://%s/%s  done \n",$rowId,$bucket,$name);

        }else {
            printf("Error :: s3 object copy :: %d :: http://%s/%s  \n",$rowId,$bucket,$name);
            exit ;
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
    //$bucket = "media1.3mik.com" ;
    $bucket = "bkp1.3mik.com" ;


    $exists = $s3->if_bucket_exists($bucket);
    if(!$exists) {
        trigger_error("S3 bucket does not exists \n" , E_USER_ERROR);
    }else {
        printf("bucket %s found \n" ,$bucket);

    }

    // + start 

    $mysqli = \com\indigloo\mysql\Connection::getInstance()->getHandle();

    /*
    $sql = "select max(id) as total from sc_media " ;
    $row = MySQL\Helper::fetchRow($mysqli, $sql);
    $total = $row["total"] ;
    // images - for bkp1.3mik.com - 5667 , for media1.3mik.com - 6231
    // thumbnail - for bkp1.3mik.com - 2000, for media1.3mik.com - 2000
    // run for stored_name and thumbnail 
	*/
    $total = 2000 ;
	
    $pageSize = 50 ;
    $pages = ceil($total / $pageSize);
    $count = 0 ;

    while($count  <= $pages ){
        $start =  ($count * $pageSize ) + 1 ;
        $end = $start + ($pageSize - 1 ) ;

        $sql = " select * from sc_media where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        printf("processing media rows between %d and %d \n",$start,$end);

        foreach($rows as $row) {
            //$s3name = $row["stored_name"];
            $s3name = $row["thumbnail"];
            $name = $row["original_name"];
            $rowId = $row["id"];

            // get mime from name.
            $mime = \com\indigloo\Util::getMimeFromName($name);
            if(empty($mime)) {
                //report it
                printf("Bad mime type for media id %d \n" ,$rowId);
                $mime = "application/octet-stream" ;
            }

            add_object_headers($s3,$bucket,$s3name,$rowId,$mime);
            sleep(1);
        }


        $count++ ;
    }


?>
