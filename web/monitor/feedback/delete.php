<?php

    //sc/monitor/feedback/delete.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    try {
   
        $gWeb = \com\indigloo\core\Web::getInstance();
        $qparams = Url::getRequestQueryParams();
        $qUrl = base64_decode($qparams["q"]);
        $id = $qparams["id"];
        $feedbackDao = new \com\indigloo\sc\dao\Feedback();
        $feedbackDao->delete($id);

        header("Location: " . $qUrl);
        exit();

    } catch(\Exception $ex) {
        $gWeb->store("global.overlay.message",$ex->getMessage());
        header("Location: " . $qUrl);
        exit(1);
    }

?>