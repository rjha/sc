<?php
    //sc/user/dashboard/posts.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Constants as Constants;

    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\sc\auth\Login as Login;
    
    
    $gSessionLogin = \com\indigloo\sc\auth\Login::getLoginInSession();
    $loginId = $gSessionLogin->id;
    $loginName = $gSessionLogin->name;

    if (is_null($loginId)) {
        trigger_error("Error : NULL login_id on user dashboard", E_USER_ERROR);
    }

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $panelId = $sticky->get("panel_id");

    $listDao = new \com\indigloo\sc\dao\Lists();
    $analyticDao = new \com\indigloo\sc\dao\Analytic();
    $counters = $analyticDao->getUserCounters($loginId);
    $list_counter = $counters["list_count"];
    

    $qparams = Url::getRequestQueryParams();
    $pageSize = Config::getInstance()->get_value("user.page.items");
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);

    $listDBRows = $listDao->getPagedOnLoginId($paginator,$loginId);
    $baseURI = "/user/dashboard/list/index.php" ;
    $fUrl = Url::current();

?>


<!DOCTYPE html>
<html>

    <head>
        <title> Lists - <?php echo $loginName ; ?>  </title>
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
                        <span class="title">My lists</span>
                        <span class="badge"><?php echo $list_counter; ?></span>
                        <span class="ml40">
                            <a class="btn-flat open-panel" rel="list-create" href="#">+ Create new list</a>
                        </span>

                    </div>
                </div>
            </div> <!-- page:header -->
           
            <?php FormMessage::render(); ?>

            <div class="row">
                <div class="span7 offset1">
                    <div id="page-message" class="hide-me"> </div>
                    <div id="list-create" class="panel panel-form">
                        <div class="wrapper">
                            <div class="floatr">
                                <span><a href="#" class="close-panel" rel="list-create">close</a> </span>
                            </div>
                         </div>

                         <form  id="form1"  name="create-form" action="/user/action/list/create.php"   method="POST">
                            <span class="faded-text">Name (letters and numbers only)</span> <br>
                            <input name="name" class="required" maxlength="64" type="text" value="<?php echo $sticky->get("name"); ?>" /> <br>
                            <span class="faded-text">Description</span>  <br>
                            <textarea name="description"><?php echo $sticky->get("description"); ?></textarea> <br>
                            <button type="submit" class="btn btn-small" name="save" value="Save"><span>Save</span></button>
                            &nbsp;
                            <a class="btn btn-small close-panel" rel="list-create">Cancel</a>
                            
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl ?>"/>
                            <input type="hidden" name="panel_id" value="list-create"/>

                        </form> <!-- form:1 -->
                    </div>
                </div>

            </div> <!-- row:action:forms -->
            

            <div class="row">
                <div class="span9 offset1">
                    
                    <div id="widgets">
                    <?php 
                        $startId = NULL;
                        $endId = NULL;
                        $gNumRecords = sizeof($listDBRows);

                        if ( $gNumRecords > 0) {
                            $startId = $listDBRows[0]["id"];
                            $endId = $listDBRows[$gNumRecords-1]["id"];

                            foreach($listDBRows as $listDBRow) {
                                echo \com\indigloo\sc\html\Lists::getWidget($listDBRow);
                            }

                        } else {
                            
                            $message = "No Lists found " ;
                            $options = array("hkey" => "dashboard.list.index");
                            echo \com\indigloo\sc\html\Site::getNoResult($message,$options);
                        }
                     ?>
                     </div> <!-- widgets -->
                        
                </div>
               
            </div>
        </div> <!-- container -->
        <?php $paginator->render($baseURI,$startId,$endId,$gNumRecords); ?>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
        <script>
            
            $(document).ready(function(){

                $("#form1").validate({
                    errorLabelContainer: $("#page-message")
                });

                //fix twitter bootstrap alerts
                webgloo.sc.dashboard.fixAlert();
                webgloo.sc.toolbar.add();

                webgloo.sc.util.initPanel('<?php echo $panelId; ?>');

                //turn off border for last widget
                $("#widgets .widget:last-child").css('border-bottom', 'none');
            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>



