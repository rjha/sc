<?php

    $feedbackDao = new \com\indigloo\sc\dao\Feedback();

	$total = $feedbackDao->getTotalCount();
	$pageSize =	20;
	$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
	$feedbackDBRows = $feedbackDao->getPaged($paginator);

    $startId = NULL ;
    $endId = NULL ;

    if(sizeof($feedbackDBRows) > 0 ) {
        $startId = $feedbackDBRows[0]['id'];
        $endId = $feedbackDBRows[sizeof($feedbackDBRows)-1]['id'];
    }

    foreach($feedbackDBRows as $feedbackDBRow) {
        echo \com\indigloo\sc\html\Feedback::get($feedbackDBRow);
    }
    
    $pageBaseUrl = $_SERVER['REQUEST_URI'];
    $paginator->render($pageBaseUrl,$startId,$endId);  

?>


