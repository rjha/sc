<?php
	use \com\indigloo\sc\auth\Login as Login ;
 
	if(!Login::isValid()){
		
		$fwd = '/user/login.php?q='.$_SERVER['REQUEST_URI'];
		header('location: '.$fwd);
		exit ;
  }

?>
