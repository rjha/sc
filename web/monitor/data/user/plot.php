<?php
    
    header('Content-type: application/json');
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    $loginDao = new \com\indigloo\sc\dao\Login();
    $rows = $loginDao->getAggregate();

    $size = sizeof($rows);
    // @see https://groups.google.com/forum/?fromgroups=#!topic/jqplot-users/fKQvDFqIMO4
    // @imp jqplot should get integer values
    // and not the "string" representation of integers!

    $y = array();
    for($index = 0 ; $index < $size ; $index++) {
        $y[$index] = intval($rows[$index]["count"]);
    }

    $series = array();
    $series[0] = $y ;
    $data = json_encode($series);
    echo $data;

?>