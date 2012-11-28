#!/usr/bin/php
<?php

    /*  

        purpose
            - one time script to load sc_activity data from 
            sc_bookmark, sc_follow, sc_post and sc_comment tables

        running this script
            - shut down the site (no new rows)
            - clean sc_activity table (if required)
            - run this script
            
        @warning @big assumption
        This script assumes that activity_id is the right sort 
        column or that activity data is sorted on time and primary 
        key ID is right proxy for that. This argument breaks down if 
        we populate sc_activity w/o preserving the base tables created_on
        column.


    */

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\Util ;

    set_exception_handler('offline_exception_handler');

    function user_bookmark_to_activity($mysqli) {
        
        $sql = "select max(id) as total from sc_bookmark " ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $activityDao = new \com\indigloo\sc\dao\Activity();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            //move likes
            $sql = " select *  from sc_bookmark where verb = 1 and   (id <= {end}) and (id >= {start}) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {


                $subjectId = $row['subject_id'];
                $ownerId = $row['owner_id'];
                $subject = $row['subject'];
                $objectId = $row['object_id'];
                $object = $row['object_title'];
                $object = Util::filterBadUtf8($object);

                $activityDao->addRow($ownerId,$subjectId,$objectId,$subject,$object,AppConstants::LIKE_VERB);

            }
            
            flush();
            sleep(1);
            $count++ ;

        }
    }

    function follower_to_activity($mysqli) {

        $sql = "select max(id) as total from sc_follow" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\Activity();


        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_follow where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
  
            foreach($rows as $row) {
                $subjectId = $row['follower_id'] ;
                $objectId = $row['following_id'] ;

                $row1 = $userDao->getOnLoginId($subjectId);
                $row2 = $userDao->getOnLoginId($objectId);
                $subject = $row1['name'];
                $object = $row2['name'];
                $verb = AppConstants::FOLLOW_VERB ;

                $ownerId  = -1 ;
                $activityDao->addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb);

            }

            flush();
            sleep(1);
            $count++ ;
        }
    }


    function comment_to_activity($mysqli) {

        $sql = "select max(id) as total from sc_comment" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\Activity();
        $postDao = new \com\indigloo\sc\dao\Post();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_comment where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
          
            foreach($rows as $row) {
                $postId = $row['post_id'];
                $postDBRow = $postDao->getOnId($postId);


                $subjectId = $row['login_id'];
                $userDBRow = $userDao->getOnLoginId($subjectId);

                $subject = $userDBRow['name'];
                $ownerId = $postDBRow['login_id'];
                
                $object = $row['title'] ;
                $object = Util::filterBadUtf8($object);

                $content = $row['description'];
                $content = Util::filterBadUtf8($content);

                $verb = AppConstants::COMMENT_VERB ;
                $objectId = $postId ;

                $activityDao->addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb,$content);

            }

            flush();
            sleep(1);
            $count++ ;
        }

    }

    function post_to_activity($mysqli) {
        
        $sql = "select max(id) as total from sc_post" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\Activity();
        
        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_post where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {

                $subjectId = $row['login_id'];
                $ownerId = $row['login_id'];
                $postId = $row['id'];
                $objectId = PseudoId::encode($postId);
    
                $userDBRow = $userDao->getOnLoginId($subjectId);
                $subject = $userDBRow['name'];

                $object = $row['title'] ;
                $object = Util::filterBadUtf8($object);
                $verb = \com\indigloo\sc\Constants::POST_VERB ;

                $activityDao->addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb);

            }
            flush();
            sleep(1);
            $count++ ;
        }

    }

    ob_end_clean();

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    
    post_to_activity($mysqli);
    sleep(1);
    comment_to_activity($mysqli);
    sleep(1);
    follower_to_activity($mysqli);
    sleep(1);
    user_bookmark_to_activity($mysqli);
    

    //close resources
    $mysqli->close();
?>
