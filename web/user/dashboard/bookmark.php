<?php
    //sc/user/dashboard/bookmark.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;

    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    use \com\indigloo\ui\Filter as Filter;

    
    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $userDao = new \com\indigloo\sc\dao\User();
    $userDBRow = $userDao->getOnLoginId($loginId);

    if (empty($userDBRow)) {
        trigger_error("No user record found for given login_id", E_USER_ERROR);
    }

    $tileOptions = ~UIConstants::TILE_ALL ;
    $pageTitle = "Saved items on 3mik" ;
    $tileOptions = UIConstants::TILE_REMOVE ;

    $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();

    //add login_id and code filters
    $model = new \com\indigloo\sc\model\Bookmark();
    $filters = array();

    //filter-1
    $filter = new Filter($model);
    $filter->add($model::SUBJECT_ID_COLUMN,Filter::EQ,$loginId);
    array_push($filters,$filter);

    //filter-2
    $filter = new Filter($model);
    $filter->add($model::VERB_COLUMN,Filter::EQ,AppConstants::SAVE_VERB);
    array_push($filters,$filter);

    $total = $bookmarkDao->getTotal($filters);
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
    $postDBRows = $bookmarkDao->getPaged($paginator,$filters);
    $pageBaseUrl = "/user/dashboard/bookmark.php";


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
        <div class="container">

            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>Saved items</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>
                <div class="span8 mh600">
                    
                    <div class="faded-text mb20">
                        The items you saved are shown here. To remove an item 
                        from saved list, do mouse over the item and click Remove.
                    </div>
                    

                    <div id="widgets">
                        <?php
                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($postDBRows) > 0 ) {
                                $startId = $postDBRows[0]['id'] ;
                                $endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
                                foreach($postDBRows as $postDBRow) {
                                    //$html = \com\indigloo\sc\html\Post::getTile($postDBRow,$tileOptions);
                                    $html = \com\indigloo\sc\html\Post::getBookmarkWidget($postDBRow);
                                    echo $html ;

                                }
                            } else {
                                $message = "No results found " ;
                                echo \com\indigloo\sc\html\Site::getNoResult($message);
                            }

                        ?>

                    </div><!-- widgets -->

                </div>

            </div>

        </div>  <!-- container -->
        
        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){

               $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                    /* @todo move colors to a css style */
                    $(this).css("background-color", "#FEFDF1");
                });

                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                    $(this).css("background-color", "#FFFFFF");
                });

                webgloo.sc.item.addActions();
                webgloo.sc.toolbar.add();

            });
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

