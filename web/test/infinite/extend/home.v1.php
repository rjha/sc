<?php


    include("sc-app.inc");
    include(APP_CLASS_LOADER);

    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;


    $postDao = new \com\indigloo\sc\dao\Post();
    $total = $postDao->getTotalCount();
    $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
    //$pageSize = Config::getInstance()->get_value("main.page.items");
    $pageSize = 5;
    $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);

    $postDBRows = $postDao->getPaged($paginator);

    $pageHeader = '';
    $pageBaseUrl = '/' ;

    $pageTitle = SeoData::getHomePageTitle();
    $metaKeywords = SeoData::getHomeMetaKeywords();
    $metaDescription = SeoData::getHomeMetaDescription();

?>

<!DOCTYPE html>
<html>

    <head>
    <title> <?php echo $pageTitle; ?>  </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $metaKeywords; ?>">
        <meta name="description" content="<?php echo $metaDescription;  ?>">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>


    </head>

     <body class="dark-body2">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <div id="tiles">

                           <?php
                            $startId = NULL;
                            $endId = NULL ;
                            if(sizeof($postDBRows) > 0 ) {
                                $startId = $postDBRows[0]['id'] ;
                                $endId =   $postDBRows[sizeof($postDBRows)-1]['id'] ;
                                foreach($postDBRows as $postDBRow) {
                                    $html = \com\indigloo\sc\html\Post::getTile($postDBRow);
                                    echo $html ;
                                }
                            }else {
                                $message = "No results found " ;
                                echo \com\indigloo\sc\html\NoResult::get($message);
                            }


                        ?>

                    </div><!-- tiles -->
                    <hr>

                </div>
            </div> <!-- row -->

            <div id="scroll-loading"> </div>

        </div>  <!-- container -->
        <hr>
        <?php $paginator->render($pageBaseUrl,$startId,$endId);  ?>


        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>
        <script type="text/javascript" src="/test/infinite/extend/jquery.infinitescroll.js"> </script>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>


        <script type="text/javascript">
            /* column width = css width + margin */
            $(function(){

                var $container = $('#tiles');

                $container.imagesLoaded(function(){
                    $container.masonry({
                        itemSelector : '.tile'
                    });
                });

                $container.infinitescroll(
                    {
                        behavior : "webgloo",
                        navSelector  	: ".pager",
                        nextSelector 	: ".pager a:last",
                        itemSelector : ".tile",
                        bufferPx : 80,

                        loading : {
                            selector : "#scroll-loading",
                            img : "/css/images/6RMhx.gif",
                            msgText: "<em>Please wait. Loading more items...</em>",
                            finishedMsg : "<b> You have reached the end of this page </b>",
                            speed: "slow"

                        }

                    },
                    function( newElements ) {
                        $(newElements).imagesLoaded(function(){
                            $container.masonry( 'appended', $(newElements));
                            $("#infscr-loading").fadeOut("slow");
                        });

                    }
                );

                $.extend($.infinitescroll.prototype,{
                    _setup_webgloo: function infscr_setup_webgloo () {
                        var opts = this.options,
                        instance = this;

                         instance.options.loading.start = function() {
                             $(opts.navSelector).hide();
                             opts.loading.msg
                             .appendTo(opts.loading.selector)
                             .show(opts.loading.speed, function () {
                             });
                         };

                        instance.options.loading.finished = function () {
                            //do nothing
                        }

                        instance.options.state.nextUrl = $(opts.nextSelector).attr('href');
                        console.log("setup: next url = " + instance.options.state.nextUrl);
                        //bind to scroll
                        instance.bind();

                    },

                    retrieve_webgloo : function infscr_retrieve_webgloo(pageNum) {

                        var opts = this.options,
                        instance = this,
                        box,
                        desturl,
                        condition ;


                        // increment the current page
                        opts.state.currPage++;

                        // if we're dealing with a table we can't use DIVs
                        box = $(opts.contentSelector).is('table') ? $('<tbody/>') : $('<div/>');

                        desturl = instance.options.state.nextUrl;
                        instance._debug('heading into ajax', desturl);
                        console.log("destination url = "+ desturl);

                        /*
                         * Earlier the plugin was using jQuery load() method on box to retrieve page fragments
                         * (using url+space+selector trick and itemSelector filtering on returned document)
                         * box.load(url,callback) method was adding the page fragment as first child of box.
                         *
                         * so we also "simulate" that behavior. we find the nextUrl from page and then
                         * use append the page fragment inside box.
                         *
                         *
                         */

                        $.ajax({
                            // params
                            url: desturl,
                            dataType: opts.dataType,
                            complete: function infscr_ajax_callback(jqXHR, textStatus) {
                                condition = (typeof (jqXHR.isResolved) !== 'undefined') ? (jqXHR.isResolved()) : (textStatus === "success" || textStatus === "notmodified");
                                if(condition) {
                                    response = '<div>' + jqXHR.responseText  + '</div>' ;
                                    instance.options.state.nextUrl = $(response).find(opts.nextSelector).attr("href");
                                    data = $(response).find(opts.itemSelector);
                                    //Do the equivalent of box.load here
                                    $(box).append(data);
                                    instance._loadcallback(box,data) ;
                                } else {
                                    instance._error('end');
                                }
                            }
                        });


                        // for manual triggers, if destroyed, get out of here
                        if (opts.state.isDestroyed) {
                            instance._debug('Instance is destroyed');
                            return false;
                        };

                        // we dont want to fire the ajax multiple times
                        opts.state.isDuringAjax = true;
                        opts.loading.start.call($(opts.contentSelector)[0],opts);


                    } //end:retrieve


                });

                //show options on hover
                $('.tile .options').hide();
                $('.tile').live("mouseenter", function() {$(this).find('.options').toggle();});
                $('.tile').live("mouseleave", function() {$(this).find('.options').toggle();});

                //Add item toolbar actions
                webgloo.sc.item.addActions();
                webgloo.sc.toolbar.add();

            });


        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
