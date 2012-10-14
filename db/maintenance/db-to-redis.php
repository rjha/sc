#!/usr/bin/php
<?php

    /*  

        Before running the script
        -----------------------------
        + sc_bookmark, sc_post, sc_comment, sc_follow table should exist
        + turn off site worker cron job (put an exit at top)
        + redis-cli : issue command flushdb - get rid of old keys 
        
        After running the script
        -------------------------------
        + remove global job queue and jobs.
        
        del  sc:global:queue:new 
        del  sc:global:jobs
        del sc:global:nextJobId

        + verify using
        
        exists sc:global:nextJobId
        exists sc:global:jobs
        exists sc:global:queue:new

        + turn on cron job
        @todo- do we need to reset sc:global:nextJobId ?

    */

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\Util ;

    set_exception_handler('offline_exception_handler');

    function user_bookmark_to_feed($mysqli) {
        
        $sql = "select max(id) as total from sc_bookmark " ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;


        $userDao = new \com\indigloo\sc\dao\User();
        $postDao = new \com\indigloo\sc\dao\Post();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_bookmark where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {


                $loginId = $row['subject_id'];
                $ownerId = $row['owner_id'];
                $name = $row['subject'];
                $itemId = $row['object_id'];
                $title = $row['object_title'];
                $title = Util::filterBadUtf8($title);

                //number 1|2 stored in table
                $action = $row['verb'];

                $verb = -1 ;
                if($action == 1 ) $verb = AppConstants::LIKE_VERB ;
                if($action == 2 ) $verb = AppConstants::SAVE_VERB ;

                $postId = PseudoId::decode($itemId);
                $image = $postDao->getImageOnId($postId);

                //push to redis
                // get image from item.
               
                $postId = PseudoId::decode($itemId);
                $image = $postDao->getImageOnId($postId);

                $activityDao->addBookmark($ownerId,$loginId,$name,$itemId,$title,$image,$verb);

            }
            
            sleep(1);
            $count++ ;

        }
    }

    function follower_to_feed($mysqli) {

        $sql = "select max(id) as total from sc_follow" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();


        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_follow where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
  
            foreach($rows as $row) {
                $followerId = $row['follower_id'] ;
                $followingId = $row['following_id'] ;

                $row1 = $userDao->getOnLoginId($followerId);
                $row2 = $userDao->getOnLoginId($followingId);
                $followerName = $row1['name'];
                $followingName = $row2['name'];
                $verb = AppConstants::FOLLOWING_VERB ;

                $activityDao->addFollower($followerId, $followerName, $followingId, $followingName, $verb);

            }

            sleep(1);
            $count++ ;
        }
    }


    function comment_to_feed($mysqli) {

        $sql = "select max(id) as total from sc_comment" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
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
                $image = $postDao->getImageOnId($postId);


                $loginId = $row['login_id'];
                $userDBRow = $userDao->getOnLoginId($loginId);
                $name = $userDBRow['name'];
                $ownerId = $postDBRow['login_id'];
                $itemId = PseudoId::encode($postId);

                $title = $row['title'] ;
                $title = Util::filterBadUtf8($title);

                $content = $row['description'];
                $content = Util::filterBadUtf8($content);

                $verb = AppConstants::COMMENT_VERB ;
                $activityDao->addComment($ownerId, $loginId, $name, $itemId, $title, $content, $image, $verb);

            }

            sleep(1);
            $count++ ;
        }

    }

    function post_to_feed($mysqli) {
        
        $sql = "select max(id) as total from sc_post" ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
        $postDao = new \com\indigloo\sc\dao\Post();

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select *  from sc_post where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);

            foreach($rows as $row) {

                $loginId = $row['login_id'];
                $postId = $row['id'];
                $itemId = PseudoId::encode($postId);
                $image = $postDao->getImageOnId($postId);
    
                $userDBRow = $userDao->getOnLoginId($loginId);
                $name = $userDBRow['name'];

                $title = $row['title'] ;
                $title = Util::filterBadUtf8($title);

                $verb = \com\indigloo\sc\Constants::POST_VERB ;
                $activityDao->addPost($loginId, $name, $itemId, $title,$image,$verb);

            }

            sleep(1);
            $count++ ;
        }

    }


    $mysqli = MySQL\Connection::getInstance()->getHandle();
    user_bookmark_to_feed($mysqli);
    sleep(1);
    follower_to_feed($mysqli);
    sleep(1);
    comment_to_feed($mysqli);
    sleep(1);
    post_to_feed($mysqli);

    //close resources
    $mysqli->close();
?>
