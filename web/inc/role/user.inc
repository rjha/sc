<?php
	use \com\indigloo\sc\auth\Login as Login ;
	use \com\indigloo\Url as Url ;
 
	if(!Login::isValid()){
        $qUrl = Url::tryQueryParam("q");
        if(is_null($qUrl)) {
            $qUrl = $_SERVER['REQUEST_URI'];
        }

        $fwd = '/user/login.php?q='.$qUrl;
		header('location: '.$fwd);
		exit ;
  }

?>
