<?php
    //sc/user/dashboard/list/detail.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\auth\Login as Login;
    use \com\indigloo\ui\form\Message as FormMessage;

    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\sc\util\PseudoId ;


    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $panelId = $sticky->get("panel_id");

    $qparams = Url::getRequestQueryParams();
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name ;

    if (is_null($loginId)) {
        trigger_error("Error : NULL or invalid login_id", E_USER_ERROR);
    }

    $plistId = Url::getQueryParam("list_id");
    $listId = PseudoId::decode($plistId);

    settype($listId,"int");

    $listDao = new \com\indigloo\sc\dao\Lists(); 
    $listDBRow = $listDao->getOnId($listId);
    $listName = $listDBRow["name"];

    //list owner check
    if(!Login::isOwner($listDBRow["login_id"])) {
        header("Location: /site/error/403.html");
        exit ;
    }
    
    $listPubUrl = sprintf("%s/pub/list/%d/%s",Url::base(),$plistId,$listDBRow["seo_name"]);

    //get items from sc_list_item table
    $model = new \com\indigloo\sc\model\Lists();
    $filter = new Filter($model);
    $filter->add($model::LIST_ID,Filter::EQ,$listId);
    
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $filters = array();
    array_push($filters,$filter);
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $itemDBRows = $listDao->getPagedItems($paginator,$filters);
    
    $baseURI = "/user/dashboard/list/detail.php";
    $fUrl = Url::current();

?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $listName; ?> </title>
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
                    <div class="row">
                        <div class="span1 offset1">
                            <input id="page-checkbox" type="checkbox" name="page-checkbox" value="1" />
                        </div>
                        <div class="span7">
                            <a class="btn-flat open-panel" rel="list-add-item" href="#">+ Add item by URL</a>
                            &nbsp;&nbsp;
                            <a class="btn-flat item-action" rel="list-delete-item" href="#">Remove items</a>
                             &nbsp;&nbsp;
                            <a class="btn-flat open-panel" rel="list-edit" href="#" >Edit list</a>
                            &nbsp;&nbsp;
                            <a class="btn-flat open-panel" rel="list-delete" href="#">Delete list</a>
                           
                           
                        </div>

                    </div>
                </div>
           
            </div> <!-- page actions -->

            <div class="row">
                <div class="span8 offset1">
                    <div id="page-message" class="hide-me"> </div>
                    <div id="list-edit" class="panel panel-form"> 
                        <div class="wrapper">
                            <div class="floatr">
                                <span><a href="#" class="close-panel" rel="list-edit">close</a> </span>
                            </div>
                         </div>

                         <form  id="form1"  name="edit-form" action="/user/action/list/edit.php"   method="POST">
                            <span class="faded-text">Name (letters and numbers only)</span> <br>
                            <input name="name" class="required" maxlength="64" type="text" value="<?php echo $sticky->get('name',$listDBRow['name']); ?>" /> <br>
                            <span class="faded-text">Description</span>  <br>
                            <textarea name="description"><?php echo $sticky->get('description',$listDBRow['description']); ?></textarea> <br>
                            <button type="submit" class="btn btn-small" name="save" value="Save"><span>Save</span></button>
                            &nbsp;
                            <a class="btn btn-small close-panel" rel="list-edit">Cancel</a>
                            
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl ?>"/>
                            <input type="hidden" name="list_id" value="<?php echo $listId ?>"/>
                            <input type="hidden" name="panel_id" value="list-edit"/>

                        </form> <!-- form:1 -->
                    </div>

                    <div id="list-add-item" class="panel panel-form">
                        <div class="wrapper">
                            <div class="floatr">
                                <span><a href="#" class="close-panel" rel="list-add-item">close</a> </span>
                            </div>
                         </div>

                         <form  id="form2"  name="add-item-form" action="/user/action/list/add-item-link.php"   method="POST">
                            <span class="faded-text">
                                Please type the 3mik URL of item and click Save
                            </span> 
                            
                            <br>
                            <input name="link" class="required" maxlength="256" type="text" value="<?php echo $sticky->get('link'); ?>" /> <br>
                            <button type="submit" class="btn btn-small" name="save" value="Save"><span>Save</span></button>
                            &nbsp;
                            <a class="btn btn-small close-panel" rel="list-add-item">Cancel</a>
                            &nbsp;
                            <a id="list-add-item-help" href="#">click here to get help on item URL</a>
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>"/>
                            <input type="hidden" name="list_id" value="<?php echo $listId ?>"/>
                            <input type="hidden" name="panel_id" value="list-add-item"/>
                        </form> <!-- form:2 -->
                    </div>

                    <div id="list-delete" class="panel panel-form">
                        <div class="wrapper">
                            <div class="floatr">
                                <span><a href="#" class="close-panel" rel="list-delete">close</a> </span>
                            </div>
                         </div>

                         <form  id="form3"  name="delete-form" action="/user/action/list/delete.php"   method="POST">
                            <p class="faded-text">
                                Are you sure you want to delete this list? 
                            </p>
                            <button type="submit" class="btn btn-small btn-danger" name="delete" value="Delete">
                                <span>Delete</span>
                            </button>
                            &nbsp;
                            <a class="btn btn-small close-panel" rel="list-delete">Cancel</a>
                            
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>"/>
                            <input type="hidden" name="list_id" value="<?php echo $listId ?>"/>
                        </form> <!-- form:3 -->
                    </div>

                    <div id="list-delete-item" class="panel panel-form">
                        <div class="wrapper">
                            <div class="floatr">
                                <span><a href="#" class="close-panel" rel="list-delete-item">close</a> </span>
                            </div>
                         </div>

                         <form  id="form4"  name="delete-item-form" action="/user/action/list/delete-items.php"   method="POST">
                            <p class="faded-text">
                                Are you sure you want to delete the selected items? 
                            </p>
                            <button type="submit" id="delete-items" class="btn btn-small btn-danger" name="delete" value="Delete">
                                <span>Delete</span>
                            </button>
                            &nbsp;
                            <a class="btn btn-small close-panel" rel="list-delete-item">Cancel</a>
                            
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>"/>
                            <input type="hidden" name="list_id" value="<?php echo $listId ?>"/>
                            <input type="hidden" name="items_json" value=''/>
                        </form> <!-- form:4 -->
                    </div>
                </div> <!-- row:forms -->
                <div class="row">

                    <div class="span8 offset1">
                    
                        <div id="widgets">
                        <?php
                            $startId = NULL;
                            $endId = NULL;
                            $gNumRecords = sizeof($itemDBRows);

                            //@imp list can be created w/o items
                            if (  $gNumRecords > 0) {
                                $startId = $itemDBRows[0]['sort_id'];
                                $endId = $itemDBRows[$gNumRecords-1]['sort_id'];
                                foreach ($itemDBRows as $itemDBRow) {
                                    //output post widget html
                                    echo \com\indigloo\sc\html\Post::getListWidget($itemDBRow);
                                }

                            } else {

                                $message = "No items in list!" ;
                                $options = array("hkey" => "dashboard.list.no-item");
                                echo \com\indigloo\sc\html\Site::getNoResult($message,$options);
                            }

                            ?>
                        </div> <!-- widgets -->
                    </div>

                    <div class="span3">

                        <div class="section1">
                            
                            <h4> <?php echo $listDBRow["name"]; ?> </h4>
                            <div class="wrap-it">
                                <span class="badge badge-warning">
                                    <?php echo $listDBRow["item_count"]; ?> items
                                </span>
                                <br>
                                <ul class="unstyled pt10">
                                    <li class="faded-text"> Public URL</li>
                                    <li>
                                        <a href="<?php echo $listPubUrl ?>" target="_blank"> <?php echo $listPubUrl; ?></a>
                                    </li>


                                </ul>
                            </div>

                            <p class="comment-text"> 
                                <?php echo $listDBRow["description"]; ?> 
                            </p>
                           

                        </div>
                    </div>
                </div>

        </div>  <!-- container -->
        
        <?php $paginator->render($baseURI, $startId, $endId, $gNumRecords); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
         
        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){

                $("#form1").validate({
                       errorLabelContainer: $("#page-message")
                });

                $("#form2").validate({
                       errorLabelContainer: $("#page-message")
                });

                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                });

                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                });

                $("#list-add-item-help").click(function(event) {
                    var message = webgloo.sc.message.HELP_LIST_ITEM_URL; 
                    webgloo.sc.dashboard.showMessage(message);

                }) ;

                webgloo.sc.toolbar.add();
                webgloo.sc.util.initPanel('<?php echo $panelId; ?>');
                
                webgloo.sc.dashboard.init();
                webgloo.sc.dashboard.setContainer("widgets");

                webgloo.sc.Lists.init();
                webgloo.sc.Lists.setContainer("widgets");
                

                //fix twitter bootstrap alerts
                webgloo.sc.dashboard.fixAlert();
                
                
                

            });
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

