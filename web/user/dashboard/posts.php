<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\ui\Filter as Filter;

    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user record found for given login_id", E_USER_ERROR);
    }

    $postDao = new \com\indigloo\sc\dao\Post();
    
    $qparams = Url::getRequestQueryParams();
    //filters
    $filters = array();
    //Always add login_id filter for user dashboard
    $model = new \com\indigloo\sc\model\Post();
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);

    $postDBRows = array();
    $total = $postDao->getTotalCount($filters);

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $postDBRows = $postDao->getPaged($paginator,$filters);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
            <div class="row">
                <div class="span9 mh600">
                    <div class="page-header">
                        <div class="faded-text">
                            All your posts are shown here. Do mouse over a post to get 
                            edit and remove links.
                        </div>
                    </div>

                    <?php
                        $startId = NULL;
                        $endId = NULL;
                        if (sizeof($postDBRows) > 0) {
                            $startId = $postDBRows[0]['id'];
                            $endId = $postDBRows[sizeof($postDBRows) - 1]['id'];
                            foreach ($postDBRows as $postDBRow) {
                                echo \com\indigloo\sc\html\Post::getWidget($postDBRow);
                            }
                        } else {
                            $message = "No posts found " ;
                            echo \com\indigloo\sc\html\NoResult::get($message);
                        }

                    ?>

                </div>
                <div class="span3">
                </div>
            </div>
        </div> <!-- container -->
        <div class="hr"> </div>
        <?php $paginator->render('/user/dashboard/posts.php', $startId, $endId); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script>
            $(document).ready(function(){
                //show options on widget hover
                $('.widget .options').hide();
                $('.widget').mouseenter(function() {
                    $(this).find('.options').toggle();
                    /* @todo move colors to a css style */
                    $(this).css("background-color", "#FEFDF1");
                });

                $('.widget').mouseleave(function() {
                    $(this).find('.options').toggle();
                    $(this).css("background-color", "#FFFFFF");
                });

                 webgloo.sc.toolbar.add();
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



