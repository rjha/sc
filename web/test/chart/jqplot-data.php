<?php
    
    $y = array();

    $index = 0 ;
    for($x = 0 ; $x< 13 ; $x += 0.5) {
        $y[$index] =  sin($x);
        $index++ ;
    }

    $series = array();
    $series[0] = $y ;
    $data = json_encode($series);
    echo $data;

?>