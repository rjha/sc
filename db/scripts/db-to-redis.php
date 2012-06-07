#!/usr/bin/php
<?php

    //pre-req: create table sc_bookmark
    //pre-req: remove everything from sc_bookmark
    //pre-req: start redis // flushdb redis

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\sc\util\PseudoId ;
    use \com\indigloo\sc\Constants as AppConstants ;

    set_error_handler('offline_error_handler');

    function user_bookmark_to_bookmark($mysqli) {
        $sql = " select * from sc_user_bookmark order by id" ;
        $rows = MySQL\Helper::fetchRows($mysqli,$sql);
        $userDao = new \com\indigloo\sc\dao\User();
        $postDao = new \com\indigloo\sc\dao\Post();

        $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();

        foreach($rows as $row) {

            $loginId = $row['login_id'];
            $itemId = $row['item_id'];

            $userDBRow = $userDao->getOnLoginId($loginId);
            $name = $userDBRow['name'];

            $postId = PseudoId::decode($itemId);
            $postDBRow = $postDao->getOnId($postId);


            $title = $postDBRow['title'];
            $ownerId = $postDBRow['login_id'];

            $action = $row['action'];
            settype($action,"integer");

            $verb = -1 ;
            if($action == 1 ) $verb = AppConstants::LIKE_VERB ;
            if($action == 2 ) $verb = AppConstants::FAVORITE_VERB ;

            //push to sc_user_bookmark
            $bookmarkDao->add($ownerId, $loginId, $name, $itemId, $title, $verb);
            //push to redis

        }

    }

    function follower_to_feed($mysqli) {
        $sql = " select * from sc_follow order by id" ;
        $rows = MySQL\Helper::fetchRows($mysqli,$sql);

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();

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
    }

    function comment_to_feed($mysqli) {
        $sql = " select * from sc_comment order by id " ;
        $rows = MySQL\Helper::fetchRows($mysqli,$sql);

        $userDao = new \com\indigloo\sc\dao\User();
        $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
        $postDao = new \com\indigloo\sc\dao\Post();

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
            $content = $row['description'];

            $verb = AppConstants::COMMENT_VERB ;
            $activityDao->addComment($ownerId, $loginId, $name, $itemId, $title, $content, $image, $verb);

        }

    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    user_bookmark_to_bookmark($mysqli);
    sleep(1);
    follower_to_feed($mysqli);
    sleep(1);
    comment_to_feed($mysqli);

?>
