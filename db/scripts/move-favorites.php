<?php 
    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
    error_reporting(-1);
    set_exception_handler('offline_exception_handler');

    /* 
        @prereq: run the insert queries in patch file
        delete from sc_list ;
        delete from sc_list_item ;
        update sc_site_counter set list_count = 0 ;
        update sc_user_counter set list_count = 0 ;
        update sc_post_counter set list_count = 0 ;
        

    */

    ob_end_clean();

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    
    // get all login_id from sc_bookmark
    // people who have saved / liked items

    $sql = " select count(id), subject_id from sc_bookmark group by subject_id " ;
    $rows = MySQL\Helper::fetchRows($mysqli,$sql);

    $listDao = new \com\indigloo\sc\dao\Lists();
    $listName = "Favorites" ;
    $listDescription = "Items that I treat with special favor!" ;
    $loginIds = array();

    foreach($rows as $row) {
        $loginId = $row["subject_id"];
        // create a Favorites list for all loginId in sc_bookmark
        $listDao->createNew($loginId,$listName,$listDescription,1);
        array_push($loginIds,$loginId);

    }

    // list added
    // now add sc_bookmark items to this list
    $t1_sql = " select id from sc_list where login_id = %d and name = '%s' " ;
    $t2_sql = " select object_id from sc_bookmark where subject_id = %d and verb = 2 " ;

    foreach($loginIds as $loginId) {
        // get list ID
        $sql = sprintf($t1_sql,$loginId, "Favorites");
        $row = MySQL\Helper::fetchRow($mysqli,$sql);
        $listId = $row["id"];

        // get bookmarked items
        $sql = sprintf($t2_sql,$loginId);
        $items = MySQL\Helper::fetchRows($mysqli,$sql);

        foreach($items as $item) {
            $listDao->addItem($loginId,$listId,$item["object_id"]);
        }
    }

?>
