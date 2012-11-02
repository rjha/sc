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
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
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
    $pageSize = 5 ;

    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $postDBRows = $postDao->getPaged($paginator,$filters);
    
    $sl = Util::tryArrayKey($_GET,'sl');
    $slclass = (!is_null($sl) && ($sl == 1 )) ? "" : "hide-me" ; 


?>


<!DOCTYPE html>
<html>

    <head>
        <title> items - <?php echo $loginName; ?>  </title>
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
            <?php FormMessage::render(); ?>

            <div class="row">
                <div id="page-action">
                    <div class="span1 offset1">
                        <input id="page-checkbox" type="checkbox" name="page-checkbox" value="1" />
                    </div>
                    <div class="span7">
                        <a class="btn btn-flat item-action" rel="list-popup" href="#" class="b btn btn-small">Add to list</a>
                    </div>
                    
                
                </div>

            </div> <!-- page actions -->

            <div class="row">
               
                <div class="span8 offset1">
                    
                    <div class="row">
                        <div id="page-message" class="hide-me"> </div>
                        <div id="list-popup" class="action-form">
                            <?php
                                
                                //copy URL parameters
                                $fparams = $qparams;
                                // unset sl param
                                unset($fparams["sl"]);
                                $qUrl = Url::createUrl("/user/dashboard/posts.php",$fparams);

                                $listDao = new \com\indigloo\sc\dao\Lists();
                                $listRows = $listDao->getOnLoginId($loginId);
                                $html = \com\indigloo\sc\html\Lists::getSelectPopup($listRows,$qUrl);
                                echo $html ;
                            ?>

                        </div>

                    </div> <!-- popups -->
                    <div id="widgets">
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            $imageData = array();

                            if (sizeof($postDBRows) > 0) {
                                $startId = $postDBRows[0]['id'];
                                $endId = $postDBRows[sizeof($postDBRows) - 1]['id'];
                                foreach ($postDBRows as $postDBRow) {
                                    //output post widget html
                                    echo \com\indigloo\sc\html\Post::getWidget($postDBRow);
                                    //get id + images_json from postDBRows 
                                    
                                    $images = json_decode($postDBRow["images_json"]);
                                    if( (!empty($images)) && (sizeof($images) > 0)) {
                                        $image = $images[0];
                                        $imgv = \com\indigloo\sc\html\Post::convertImageJsonObj($image);
                                        //id vs. source,thumbnail
                                        $imageData[$postDBRow["pseudo_id"]] = array("thumbnail" => $imgv["thumbnail"]) ;

                                    }

                                }
                            } else {
                                $message = "No posts found " ;
                               echo \com\indigloo\sc\html\Site::getNoResult($message);
                            }

                            $strImageJson = json_encode($imageData);
                            $strImageJson = \com\indigloo\Util::formSafeJson($strImageJson);

                        ?>
                    </div>

                </div>
                <div class="span3">
                    
                </div>
               
            </div>
        </div> <!-- container -->
        
        <?php $paginator->render('/user/dashboard/posts.php', $startId, $endId); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script src="/js/sc.js" type="text/javascript"> </script>

        <script>
            
            $(document).ready(function(){
                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                });

                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                });

                
                webgloo.sc.toolbar.add();

                var containerId = "widgets" ;
                webgloo.sc.dashboard.init(containerId);
                //fix twitter bootstrap alerts
                webgloo.sc.dashboard.fixAlert();

                webgloo.sc.Lists.init(containerId);
                webgloo.sc.Lists.debug = false ;
                webgloo.sc.Lists.strImageJson = '<?php echo $strImageJson; ?>' ;

                try{
                    webgloo.sc.Lists.imageDataObj = JSON.parse(webgloo.sc.Lists.strImageJson) ;
                    webgloo.sc.Lists.imageError = 0 ;
                } catch(ex) {
                    console.log("error : not able to parse image data json string");
                    webgloo.sc.Lists.imageError = 1 ;
                }

            });



        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



