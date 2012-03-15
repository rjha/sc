<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
       
	error_reporting(-1);
    
    //dump id,description,group
    $sql = " select id,description,group_slug from sc_post order by id desc ";
    $mysqli = MySQL\Connection::getInstance()->getHandle();
    $rows = MySQL\Helper::fetchRows($mysqli, $sql);

    foreach($rows as $row) {
        //dump csv
        printf("%d,%s,%s \n",$row['id'],$row['description'],$row['group_slug']);
    } 

?>
