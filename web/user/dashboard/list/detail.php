<?php
    //sc/user/dashboard/bookmark.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\ui\Filter as Filter;


    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $listId = $qparams["list_id"];
    settype($listId,"int");

    $listDao = new \com\indigloo\sc\dao\Lists();

    $listDBRow = $listDao->getOnId($listId);
    
    if(!Login::isOwner($listDBRow["login_id"])) {
        header("Location: /site/error/403.html");
        exit ;
    }

    /*
    $model = new \com\indigloo\sc\model\Lists();
    $filters = array();

    //filter-1
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID_COLUMN,Filter::EQ,$loginId);
    array_push($filters,$filter);

    //filter-2
    $filter = new Filter($model);
    $filter->add($model::ID_COLUMN,Filter::EQ,$listId);
    array_push($filters,$filter);

    $total = $listDao->getTotalItems($filters);
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
    $itemDBRows = $listDao->getPagedItems($paginator,$filters);
    */
    
    $pageBaseUrl = "/user/dashboard/list/detail.php";
    
    


?>
<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $pageTitle; ?> </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        
    </head>

     <body>
       <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">
            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                 <div class="span12">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span8 offset1">
                    <h3> <?php echo $listDBRow["name"]; ?> </h3>
                </div>
                <div class="span3">

                </div>
            </div>

        </div>  <!-- container -->
        <!-- @todo add paginator -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
            });
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

