<?php
    //sc/user/dashboard/comments.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\ui\Filter as Filter;
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
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

    $commentDao = new \com\indigloo\sc\dao\Comment() ;

    //Add login_id filter
    $model = new \com\indigloo\sc\model\Comment();
    $filters = array();
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);

    $total = $commentDao->getTotalCount($filters);
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
    $commentDBRows = $commentDao->getPaged($paginator,$filters);
   
?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - user <?php echo $userDBRow['name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 

         <script>
            $(document).ready(function(){
                //show options on widget hover
                $('.widget .options').hide();
                $('.widget').mouseenter(function() {
                    $(this).find('.options').toggle();
                    $(this).css("background-color", "#F0FFFF");
                });
                $('.widget').mouseleave(function() {
                    $(this).find('.options').toggle();
                    $(this).css("background-color", "#FFFFFF");
                });
            });

        </script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                     <?php  include('inc/menu.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9 mh600">
                    <?php
                        $startId = NULL ;
                        $endId = NULL ;

                        if(sizeof($commentDBRows) > 0 ) {
                            $startId = $commentDBRows[0]['id'] ;
                            $endId =   $commentDBRows[sizeof($commentDBRows)-1]['id'] ;
                            foreach($commentDBRows as $commentDBRow){
                                echo \com\indigloo\sc\html\Comment::getWidget($commentDBRow);
                            }
                        } else {
                            $message = "No comments found " ;
                            echo \com\indigloo\sc\html\NoResult::get($message);
                        }

                    ?>


                </div>
                <div class="span3">
                </div>
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/user/dashboard/comments.php', $startId, $endId); ?>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
