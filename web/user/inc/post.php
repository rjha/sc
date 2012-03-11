<?php

	
    use com\indigloo\Configuration as Config;

	$questionDao = new \com\indigloo\sc\dao\Question() ;
	
	$filter = array($questionDao::LOGIN_ID_COLUMN => $loginId);
	$total = $questionDao->getTotalCount($filter);

	$pageSize =	Config::getInstance()->get_value("user.page.items");
	$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
	$questionDBRows = $questionDao->getPaged($paginator,$filter);

?>

<div id="post">
	<h1>Posts</h1>
	<?php 
		$startId = NULL ;
		$endId = NULL ;
		if(sizeof($questionDBRows) > 0 ) { 
			$startId = $questionDBRows[0]['id'] ;
			$endId =   $questionDBRows[sizeof($questionDBRows)-1]['id'] ;
		}	

		foreach($questionDBRows as $questionDBRow){
			echo \com\indigloo\sc\html\Question::getWidget($gSessionLogin,$questionDBRow);
		}
	?>
</div>

<?php $paginator->render('/user/dashboard.php',$startId,$endId);  ?>



