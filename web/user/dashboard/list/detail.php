<?php
    //sc/user/dashboard/bookmark.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\Filter as Filter;


    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name ;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $listId = $qparams["list_id"];
    settype($listId,"int");

    $listDao = new \com\indigloo\sc\dao\Lists(); 
    $listDBRow = $listDao->getOnId($listId);
    
    //owner check
    if(!Login::isOwner($listDBRow["login_id"])) {
        header("Location: /site/error/403.html");
        exit ;
    }
    
    //get total items in list from sc_list table
    $model1 = new \com\indigloo\sc\model\Lists();
    $filter1 = new Filter($model1);
    $filter1->add($model1::LIST_ID,Filter::EQ,$listId);
    $total = $listDao->getTotalItems(array($filter1));

    //get items from sc_list_item table
    $model2 = new \com\indigloo\sc\model\ListItem();
    $filter2 = new Filter($model2);
    $filter2->add($model2::LIST_ID,Filter::EQ,$listId);
    
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
    $itemDBRows = $listDao->getPagedItems($paginator,array($filter2));
    
    $pageBaseUrl = "/user/dashboard/list/detail.php";
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $listDBRow["name"]; ?> </title>
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
                        <a class="btn btn-flat open-action" rel="list-add-item" href="#">Add +</a>
                        &nbsp;&nbsp;
                        <a class="btn btn-flat item-action" rel="list-delete" href="#">Delete -</a>
                         &nbsp;&nbsp;
                        <a class="btn btn-flat open-action" rel="list-edit" href="#" >Edit list</a>
                        &nbsp;&nbsp;
                        <a class="btn btn-flat open-action" rel="list-delete" href="#">Delete list</a>
                       
                       
                    </div>
                </div>
           
            </div> <!-- page actions -->

            <div class="row">

                <div class="span8 offset1">
                   <div class="row">
                        <div id="page-message" class="hide-me"> </div>
                        <div id="list-edit" class="action-form"> 
                            List edit 
                        </div>
                        <div id="list-add-item" class="action-form">
                            List add item
                        </div>
                        <div id="list-delete" class="action-form">
                            List delete
                        </div>

                    </div> <!-- popups -->
                    
                    <div id="widgets" class="mt20">
                    <?php
                        $startId = NULL;
                        $endId = NULL;
                        
                        //@imp list cannot be created w/o items
                        if (sizeof($itemDBRows) > 0) {
                            $startId = $itemDBRows[0]['id'];
                            $endId = $itemDBRows[sizeof($itemDBRows)-1]['id'];
                            foreach ($itemDBRows as $itemDBRow) {
                                //output post widget html
                                echo \com\indigloo\sc\html\Post::getListWidget($itemDBRow,0);
                            }

                        } 

                        ?>
                    </div> <!-- widgets -->
                </div>
                <div class="span3">
                     <div class="section1">
                        <span class="faded-text"> List Name </span> <br>
                        <h4> <?php echo $listDBRow["name"]; ?> </h4>
                        <span class="faded-text"> Items </span> <br>
                        <h4> <?php echo $listDBRow["item_count"]; ?> </h4>
                        <span class="faded-text"> created on </span> <br>
                        <h4> <?php echo \com\indigloo\sc\util\Formatter::convertDBTime($listDBRow["created_on"]); ?> </h4>

                    </div>
                </div>
            </div>

        </div>  <!-- container -->
        <?php $paginator->render($pageBaseUrl, $startId, $endId); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script src="/js/sc.js" type="text/javascript"> </script>

        <script type="text/javascript">
            /* column width = css width + margin */
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

            });
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

