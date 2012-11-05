<?php
    //sc/user/dashboard/bookmark.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;

    
    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $popupId = $sticky->get("popup_id");
    $strPopupObj = '{}' ;

    if(!Util::tryEmpty($popupId)) {
        $popupObj = new \stdClass ;
        $popupObj->id = $popupId ;
        $strPopupObj = json_encode($popupObj);
    }

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

    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $postDBRows = $bookmarkDao->getPaged($paginator,$filters);
    $pageBaseUrl = "/user/dashboard/bookmark.php";
    $qUrl = base64_encode(Url::current());


?>
<!DOCTYPE html>
<html>

    <head>
        <title> saved items of <?php echo $gSessionLogin->name; ?> </title>
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
           
            </div>

            <div class="row">
                <div class="span8 offset1 mh600">
                    <div class="row">
                        <div id="page-message" class="hide-me"> </div>
                        <div id="list-popup" class="action-form">
                            <?php
                                
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
                                    echo \com\indigloo\sc\html\Post::getBookmarkWidget($postDBRow,0);

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
                                $message = "No items found " ;
                                echo \com\indigloo\sc\html\Site::getNoResult($message);
                            }

                            $strImageJson = json_encode($imageData);
                            $strImageJson = \com\indigloo\Util::formSafeJson($strImageJson);

                        ?>
                    </div> <!-- widgets -->

                </div>

            </div>

        </div>  <!-- container -->
        
        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
           
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

                try{
                    var popupObj = JSON.parse('<?php echo $strPopupObj; ?>') ;
                    if(popupObj.hasOwnProperty("id")){
                        $("#" + popupObj["id"]).show();
                    }
                    
                } catch(ex) {
                    console.log("error : parsing popup string");
                }

            });
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

