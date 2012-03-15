<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
       
	error_reporting(-1);

    $lines = file("groups.csv");
    foreach($lines as $line){
        $columns = explode(",",$line);

        $slug = trim($columns[2]);
        $id = trim($columns[0]);

        $sql = sprintf("update sc_post set group_slug = '%s' where id = %d ; \n",$slug,$id);
        echo $sql;
        echo "\n" ; 
        if($columns[0] < 1000) break;
    }

    
?>
