<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\auth\Login as Login;
    
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
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

    
    $sl = Util::tryArrayKey($_GET,'sl');
    $slclass = (!is_null($sl) && ($sl == 1 )) ? "" : "hide-me" ; 


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

            <div class="row">
                <div class="span12">
                 <?php include(APP_WEB_DIR . '/inc/navigation/dashboard.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>My items</h2>
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/user/dashboard/inc/menu.inc'); ?>
                </div>

                <div class="span8 mh600">
                    
                    <?php FormMessage::render(); ?>
                    <div class="row">
                        <div id="page-action" class="section4">
                            <div class="span1">
                                <input id="page-checkbox" type="checkbox" name="page-checkbox" value="1" />
                            </div>
                            <div class="span7">
                                <a id="open-list-popup" href="#" class="b btn btn-small">Add to list</a>
                                &nbsp;&nbsp;
                                <a id="item-delete" href="#" class="b btn btn-small">Delete</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="page-message" class="color-red ml20"> </div>
                        <div id="list-container" class="<?php echo $slclass; ?>">
                            <?php
                                $qUrl = \com\indigloo\Url::current();
                                $listDao = new \com\indigloo\sc\dao\Lists();
                                $listRows = $listDao->getOnLoginId($loginId);
                                $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$qUrl);
                                echo $html ;
                            ?>

                        </div>


                    </div>
                    <div id="widgets">
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
                               echo \com\indigloo\sc\html\Site::getNoResult($message);
                            }

                        ?>
                    </div>

                </div>
               
            </div>
        </div> <!-- container -->
        <div class="hr"> </div>
        <?php $paginator->render('/user/dashboard/posts.php', $startId, $endId); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script src="/js/sc.js" type="text/javascript"> </script>

        <script>
            
            $(document).ready(function(){
                //fix twitter bootstrap alerts
                webgloo.sc.util.fixAlert();
                // initialize page level checkboxes
                webgloo.sc.util.initPageCheckbox("#widgets");
                webgloo.sc.toolbar.add();
                //initialize lists
                webgloo.sc.Lists.init("#widgets");
                webgloo.sc.Lists.debug = false ;
                
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



