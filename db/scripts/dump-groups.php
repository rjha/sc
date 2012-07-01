<?php 
    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
    error_reporting(-1);
    set_exception_handler('offline_exception_handler');

    function process_groups($mysqli) {
        //process groups 
        $sql = " select post_id from sc_site_tracker where group_flag = 0 order by id desc limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $groupDao = new \com\indigloo\sc\dao\Group();

        foreach($rows as $row) {
            $postId = $row["post_id"];
            $groupDao->process($postId);
        }
    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    //find out the max(id) from sc_post
    for($i = 0 ; $i < 36 ; $i++){
        process_groups($mysqli);
        sleep(2);
    }

?>
