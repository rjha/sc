<?php 
    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
    error_reporting(-1);
    set_exception_handler('offline_exception_handler');
    //@prereq: run the insert queries in patch file
    /*

    + reset counters

    update sc_user_counter set post_count = 0 ;
    update sc_user_counter set comment_count = 0 ;
    update sc_user_counter set like_count = 0 ;
    update sc_user_counter set follower_count = 0 ;
    update sc_user_counter set following_count = 0 ;
    update sc_post_counter set comment_count = 0 , like_count = 0 ;

    */

    $mysqli = MySQL\Connection::getInstance()->getHandle();

    $sql = "  select count(id) as count, login_id from sc_post group by login_id " ;
    $rows = MySQL\Helper::fetchRows($mysqli,$sql);
    $t1 = " update sc_user_counter set post_count = %d where login_id = %s ; " ;

    foreach($rows as $row) {
        $loginId = $row["login_id"];
        $count = $row["count"];
        $t1sql = sprintf($t1,$count,$loginId) ;
        printf("%s \n",$t1sql);

    }
    
    printf("\n\n");

    $sql = " select post_id, login_id from sc_comment " ;
    $rows = MySQL\Helper::fetchRows($mysqli,$sql);
    $t1 = " update sc_user_counter set comment_count = comment_count + 1 where login_id = %s ; " ;
    $t2 = " update sc_post_counter set comment_count = comment_count + 1 where post_id = %s ;" ;

    foreach($rows as $row) {
        $t1sql = sprintf($t1,$row["login_id"]) ;
        $t2sql = sprintf($t2,$row["post_id"]) ;
        printf("%s \n",$t1sql);
        printf("%s \n",$t2sql);
    }

    printf("\n\n");

    /*  
        Do not select items that were liked but deleted in the iterim
        select subject_id, object_id, verb  from sc_bookmark b,sc_post p 
        where b.object_id = p.pseudo_id ;
        */

    $sql = "select subject_id, object_id  from sc_bookmark b,sc_post p " ;
    $sql .= " where b.object_id = p.pseudo_id and b.verb = 1 " ;

    $rows = MySQL\Helper::fetchRows($mysqli,$sql);

    $t11 = " update sc_user_counter set like_count = like_count + 1 where login_id = %s ; " ;
    $t21 = " update sc_post_counter set like_count = like_count + 1 where post_id = %s ; " ;
    

    foreach($rows as $row) {
        $t1sql = sprintf($t11,$row["subject_id"]) ;
        $t2sql = sprintf($t21,$row["object_id"]) ;
        printf("%s \n",$t1sql);
        printf("%s \n",$t2sql);
    }


    printf("\n\n");

    /* no concept of deleting the user in system as on 22 nov. 2012 */
    $sql = " select follower_id, following_id from sc_follow " ;
    $rows = MySQL\Helper::fetchRows($mysqli,$sql);

    $t1 = " update sc_user_counter set follower_count = follower_count + 1 where login_id = %s ; ";
    $t2 = " update sc_user_counter set following_count = following_count + 1 where login_id = %s ; ";

    foreach($rows as $row) {
        $t1sql = sprintf($t1,$row["following_id"]) ;
        $t2sql = sprintf($t2,$row["follower_id"]) ;
        printf("%s \n",$t1sql);
        printf("%s \n",$t2sql);
    }

?>
