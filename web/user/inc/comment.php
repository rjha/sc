<?php

	use com\indigloo\Configuration as Config;

	$commentDao = new \com\indigloo\sc\dao\Comment() ;
		
	$filter = array($commentDao::LOGIN_ID_COLUMN => $loginId);
	$total = $commentDao->getTotalCount($filter);

	$pageSize =	Config::getInstance()->get_value("user.page.items");
	$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
	$commentDBRows = $commentDao->getPaged($paginator,$filter);

?>

<div id="comment">
	<h1>Comments</h1>
	<?php 
		$startId = NULL ;
		$endId = NULL ;

		if(sizeof($commentDBRows) > 0 ) { 
			$startId = $commentDBRows[0]['id'] ;
			$endId =   $commentDBRows[sizeof($commentDBRows)-1]['id'] ;
		}	

		foreach($commentDBRows as $commentDBRow){
			echo \com\indigloo\sc\html\Comment::getWidget($gSessionLogin,$commentDBRow);
		}

	?>

</div>

<?php $paginator->render('/user/dashboard.php',$startId,$endId);  ?>

