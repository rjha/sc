<?php

	
    use com\indigloo\Configuration as Config;

	$postDao = new \com\indigloo\sc\dao\Post() ;
	
	$filter = array($postDao::LOGIN_ID_COLUMN => $loginId);
	$total = $postDao->getTotalCount($filter);

	$pageSize =	Config::getInstance()->get_value("user.page.items");
	$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
	$postDBRows = $postDao->getPaged($paginator,$filter);

?>

<div id="post">
	<h1>Posts</h1>
	<?php 
		$startId = NULL ;
		$endId = NULL ;
		if(sizeof($postDBRows) > 0 ) { 
			$startId = $postDBRows[0]['id'] ;
			$endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
		}	

		foreach($postDBRows as $postDBRow){
			echo \com\indigloo\sc\html\Post::getWidget($gSessionLogin,$postDBRow);
		}
	?>
</div>

<?php $paginator->render('/user/dashboard.php',$startId,$endId);  ?>



