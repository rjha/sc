<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\ui\Pagination as Pagination;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $groups = $groupDao->getRandom(50);
    include($_SERVER['APP_WEB_DIR'].'/group/inc/body.inc');

?>
       </div> <!-- container -->
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



