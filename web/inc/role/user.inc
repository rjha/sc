<?php
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
 
    if(!Login::hasSession()) {
        /* 
         * do not use Url::current() as default q params if we have 
         * none coming in the request. the issue is that this role/user.inc
         * file can be included on forms also and we do not want to redirect
         * back to form pages. 
         * 
         */

        $qUrl = Url::tryBase64QueryParam("q", "/");
        $fwd = '/user/login.php?q='. $qUrl;
        header('location: '.$fwd);
        exit ;
  }

?>
