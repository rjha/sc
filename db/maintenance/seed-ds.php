#!/usr/bin/php
<?php

    /*
     * script to populate sc data structures.
     */

	include('sc-app.inc');
    include(APP_CLASS_LOADER);
  	include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\Constants as AppConstants ;

    set_exception_handler("offline_exception_handler");

    function add_set($mysqli,$class,$name,$key,$size) {
    	
    	$hash = md5(trim($key), TRUE);
        $sql = " insert into sc_ds_meta(name,dskey,hash,max_size,class,container,created_on)" ;
        $sql .= " values(?,?,?,?,?,now()) ";

        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssiss",$name,$key,$hash,$size, $class,"set");
            $stmt->execute();

            if ($mysqli->affected_rows != 1) {
                MySQL\Error::handle($stmt);
            }

            $stmt->close();
        } else {
            MySQL\Error::handle($mysqli);
        }
	 	

	}


	$mysqli = MySQL\Connection::getInstance()->getHandle();

    $name = "Featured Posts" ;
    $key = AppConstants::SET_FEATURED_POST ;
    add_set($mysqli,"root", $name,$key,37);

    $name = "Weekly Newsletter" ;
    $key = AppConstants::SET_WEEK_NEWS ;
    add_set($mysqli,"root",$name,$key,37);


