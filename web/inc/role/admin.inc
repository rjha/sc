<?php
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
 
    if(!Login::isAdmin()) {
        $qUrl = Url::tryQueryParam("q");
        if(is_null($qUrl)) {
            $qUrl = base64_encode($_SERVER['REQUEST_URI']);
        }

        $fwd = '/user/login.php?q='. $qUrl ;
        header('location: '.$fwd);
        exit ;
  }

?>
