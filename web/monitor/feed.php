<?php

    //sc/monitor/feed.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\sc\html\feed as feed ;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\Constants as AppConstants;

    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;


    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    $panelId = $sticky->get("panel_id");

    $feedDao = new \com\indigloo\sc\dao\Activity();
    $feedDataObj = $feedDao->getGlobalFeeds(100);
    $fUrl = Url::current();

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Activity feeds  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>


    </head>

    <body>
        <style>
            /* @inpage @hardcoded */
            .widget { padding:0;}
            #page-action { margin-left:10px; border-top: 0;}
        </style>

        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR . '/monitor/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                <?php include(APP_WEB_DIR.'/monitor/inc/top-unit.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header">
                        <h2>Activities</h2>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>

                <div class="span8 offset1">

                    <div class="row">
                        <?php FormMessage::render(); ?>
                        <div id="page-action">
                            
                            <div class="row">
                                <div class="span1">
                                    <input id="page-checkbox" type="checkbox" name="page-checkbox" value="1" />
                                </div>
                                <div class="span7">
                                    <a class="btn item-action" rel="delete-form-div" href="#">Remove</a>
                                </div>

                            </div>
                        </div><!-- page:action -->
                        <div id="page-message" class="hide-me"> </div>
                        <div id="delete-form-div" class="panel panel-form">
                            <div class="wrapper">
                                <div class="floatr">
                                    <span><a href="#" class="close-panel" rel="delete-form-div">close</a> </span>
                                </div>
                             </div>

                             <form  id="form1"  name="delete-form" action="/monitor/form/feed/delete.php"   method="POST">
                                <p class="faded-text">
                                    Are you sure you want to delete the selected items? 
                                </p>
                                <button type="submit" id="delete-items" class="btn btn-small btn-danger" name="delete" value="Delete">
                                    <span>Delete</span>
                                </button>
                                &nbsp;
                                <a class="btn btn-small close-panel" rel="delete-form-div">Cancel</a>
                                
                                <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>"/>
                                <input type="hidden" name="items" value="" />
                            </form>
                        </div> <!-- delete-form -->

                    </div> <!-- row:1 -->

                    <div class="row">

                    
                        <div id="feeds" class="feeds">
                            <?php
                            $processor1 = new feed\PostProcessor();
                            $processor2 = new feed\GraphProcessor();
                            $processor = NULL ;

                            foreach($feedDataObj->feeds as $index => $feed) {
                                try{

                                    $feedObj = json_decode($feed);
                                    $processor = 
                                    ($feedObj->verb == AppConstants::FOLLOW_VERB) ? $processor2 : $processor1 ;
                                    $content = $processor->process($feedObj);
                                    echo \com\indigloo\sc\html\Activity::getAdminWidget($index,$content);
                                    

                                } catch(\Exception $ex) {
                                    $content = "error parsing feed: ".$feed ;
                                    echo \com\indigloo\sc\html\Activity::getAdminWidget($index,$content);
                                }
                            }

                            ?>
                        </div> <!-- feeds -->
                    </div> <!-- row:2 -->

                </div>
                
            </div>
        </div> <!-- container -->

         <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
         
        <script type="text/javascript">
            /* column width = css width + margin */
            $(document).ready(function(){

                webgloo.sc.util.initPanel('<?php echo $panelId; ?>');
                
                webgloo.sc.dashboard.init();
                webgloo.sc.dashboard.fixAlert();
                webgloo.sc.dashboard.setContainer("feeds");
                
                $("#delete-items").click(function(event){
                    var itemIds = webgloo.sc.dashboard.getCheckedItems("feeds");
                    if(itemIds.length > 0 ) {
                       //submit #form1 or delete-form
                        var frm = document.forms["delete-form"];
                        frm.items.value = JSON.stringify(itemIds) ;
                        $("#form1").submit();
                    }

                });


            });
        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


