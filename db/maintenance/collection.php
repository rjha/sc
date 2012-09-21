#!/usr/bin/php
<?php
	include('sc-app.inc');
    include(APP_CLASS_LOADER);
  	include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\Constants as AppConstants ;

    set_exception_handler("offline_exception_handler");

    function add_set($mysqli,$name,$key,$card) {
    	
    	$hash = md5(trim($key), TRUE);
        $sql = " insert into sc_set(name,skey,shash,card,created_on)" ;
        $sql .= " values(?,?,?,?,now()) ";

        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssi",$name,$key,$hash,$card);
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
    $key = AppConstants::SYS_FP_SET ;
    add_set($mysqli,$name,$key,37);

    $name = "Weekly Newsletter" ;
    $key = AppConstants::SYS_WNEWS_SET ;
    add_set($mysqli,$name,$key,37);


