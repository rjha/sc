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
   
    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();

    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $commentDao = new \com\indigloo\sc\dao\Comment() ;

    //Add login_id filter
    $model = new \com\indigloo\sc\model\Comment();
    $filters = array();
    $filter = new Filter($model);
    $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
    array_push($filters,$filter);

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $commentDBRows = $commentDao->getPaged($paginator,$filters);
    
    $baseURI = "/user/dashboard/comments.php" ;

?>


<!DOCTYPE html>
<html>

    <head>
        <title> comments <?php echo $loginName; ?>  </title>
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
                <div class="span12">
                    <div class="page-header">
                        <h4> Comments </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span8 offset1">
                    
                    
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
                            echo \com\indigloo\sc\html\Site::getNoResult($message);
                        }

                    ?>


                </div>
                
            </div>
        </div> <!-- container -->

        <?php if(sizeof($commentDBRows) >= $pageSize)
                $paginator->render($baseURI,$startId,$endId); ?>
        
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

         <script>
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

                 webgloo.sc.toolbar.add();
            });

        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
