#!/usr/bin/php
<?php 

	include('sc-app.inc');
	include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;

    set_error_handler('offline_error_handler');

    function process_sites($mysqli) {
        //process sites
        $sql = " select post_id from sc_site_tracker where site_flag = 0 order by id limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $siteDao = new \com\indigloo\sc\dao\Site();

        foreach($rows as $row) {
            $postId = $row["post_id"];
            $siteDao->process($postId);
            sleep(1);
        }
    }

    function process_groups($mysqli) {
        //process sites
        $sql = " select post_id from sc_site_tracker where group_flag = 0 order by id limit 50";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        $groupDao = new \com\indigloo\sc\dao\Group();

        foreach($rows as $row) {
            $postId = $row["post_id"];
            $groupDao->process($postId);
            sleep(1);
        }
    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    process_sites($mysqli);

   ?>
