<?php

    //sc/monitor/users.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\sc\util\PseudoId ;

     //url decoded parameters
   
    $qparams = Url::getRequestQueryParams();
  
    //copy URL parameters
    $fparams = $qparams;
    // unset extra ft params and search token param
    unset($fparams["ft"]);
    unset($fparams["gt"]);
    //ft urls start with page 1
    $fparams["gpage"] = 1 ;
    //create filter Urls
    $ftBaseUrl = Url::createUrl("/monitor/users.php",$fparams);
    
    //search clear link
    $sparams = $qparams ;
    unset($sparams["gt"]);
    $clearSearchUrl = Url::createUrl("/monitor/users.php",$sparams);


    //filters
    $filters = array();
    $model = new \com\indigloo\sc\model\User();
    $ft = Url::tryQueryParam("ft");
    $ftname = "";
    $gtoken = Util::tryArrayKey($qparams,"gt");
    $userId = NULL ;

    if( empty($ft) && 
        (strlen($gtoken) > 5)
        && (strcmp(substr($gtoken,0,5), "user:") == 0)){
        $ft = "user" ;
        $userId = substr($gtoken,5);
        //reset search token
        $gtoken = NULL ;
    }

    if(empty($ft) && !empty($gtoken)) {
        $ft = "name" ;
    }

    if(!is_null($ft)) {
        switch($ft){

            case "24HR" :
                $filter = new Filter($model);
                $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
                array_push($filters,$filter);
                $ftname = "Last 24HR" ;
            break;

            case "banned" :
                $filter = new Filter($model);
                $filter->add($model::BANNED,Filter::EQ,1);
                array_push($filters,$filter);
                $ftname = "Banned" ;
            break;

            case "tainted" :
                $filter = new Filter($model);
                $filter->add($model::TAINTED,Filter::EQ,1);
                array_push($filters,$filter);
                $ftname = "Tainted" ;
            break;

            case "name" :
                $filter = new Filter($model);
                $filter->add($model::USER_NAME,Filter::LIKE,$gtoken);
                array_push($filters,$filter);
                $ftname = "name:".$gtoken;
            break;
            case "user" :
                $filter = new Filter($model);
                $loginId = PseudoId::decode($userId);
                $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
                array_push($filters,$filter);
                $ftname = "user:".$userId ;
            break;
            default:
            break;
        }
    }


    $userDBRows = array();
    $userDao = new \com\indigloo\sc\dao\User();
    $pageSize = Config::getInstance()->get_value("user.page.items");

    $total = $userDao->getTotal($filters);
    $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
    $userDBRows = $userDao->getPaged($paginator,$filters);
    $gtoken = "" ;
    $baseURI = "/monitor/users.php";

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - users in system</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <link rel="stylesheet" type="text/css" href="/3p/jquery/jqplot/jquery.jqplot.min.css" />
        <script language="javascript" type="text/javascript" src="/3p/jquery/jqplot/jquery.jqplot.min.js"></script>


    </head>

    <body>
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
                        <h2>Users</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span2">
                    <?php include(APP_WEB_DIR.'/monitor/inc/menu.inc'); ?>
                </div>
                <div class="span7">

                     <div class="row">
                        <div class="span4">
                            <form method="GET" action="<?php echo $clearSearchUrl; ?>">
                                <input id="site-search" name="gt" type="text" class="search-query" placeholder="Search...">
                                <input type="hidden" name="ft" value="<?php echo $ft; ?>"/>
                            </form>

                        </div>

                        <div class="span3">
                            <div class="faded-text">
                                <ul class="unstyled">
                                    <li> type user:&lt;user_id&gt;, e.g. user:9293 for a single user </li>
                                    
                                </ul> 
                            </div>
                           
                        </div>

                    </div> <!-- row -->

                    <div class="p10">
                        <span class="b"> Total: <?php echo $total; ?> </span>
                        &nbsp;
                        <span class="color-red">
                            filters (<?php echo $ftname; ?>)
                        </span>
                        &nbsp;
                        <a href="/monitor/users.php">All Users</a>
                        &nbsp;|&nbsp;
                        <a href="/monitor/users.php?ft=24HR">Last 24HR</a>
                        &nbsp;|&nbsp;
                        <a href="/monitor/users.php?ft=banned">Banned</a>
                         &nbsp;|&nbsp;
                        <a href="/monitor/users.php?ft=tainted">Tainted</a>

                    </div>
 
                    <div class="mt20">
                        <div id="chartdiv" style="height:225px;width:450px; "></div>
                        <?php

                            $startId = NULL;
                            $endId = NULL;

                            if (sizeof($userDBRows) > 0) {
                                $startId = $userDBRows[0]['id'];
                                $endId = $userDBRows[sizeof($userDBRows) - 1]['id'];
                            }
                            
                            foreach ($userDBRows as $userDBRow) {
                                echo \com\indigloo\sc\html\User::getAdminWidget($userDBRow);
                            }

                                
                        ?>
                    </div>
                </div>
                 
            </div>
        </div> <!-- container -->
        <div class="mt20">
        <?php if(sizeof($userDBRows) >= $pageSize) 
            $paginator->render($baseURI,$startId,$endId);  ?>

        </div>

        <script>
            $(document).ready(function(){
                //show options on widget hover
                $('.widget').mouseenter(function() {
                    $(this).find('.options').css("visibility", "visible");
                    $(this).css("background-color", "#FEFDF1");
                });
                $('.widget').mouseleave(function() {
                    $(this).find('.options').css("visibility", "hidden");
                    $(this).css("background-color", "#FFFFFF");
                }); 

                webgloo.sc.item.addAdminActions();

                var ajaxDataRenderer = function(url, plot, options) {
                    var ret = null;
                    $.ajax({
                      async: false,
                      url: url,
                      dataType:"json",
                      success: function(data) {
                        ret = data;
                      }
                    });
                    return ret;
                };
         
                // The url for our json data
                var jsonurl = "/monitor/data/user/plot.php";

                var plot2 = $.jqplot('chartdiv', jsonurl,{
                    title: "past 2 weeks",
                    seriesColors:["red"],
                    dataRenderer: ajaxDataRenderer,
                    grid: {
                        drawGridLines: false
                    },
                    axes: {
                        xaxis: {
                            show: false 
                        }
                    },
                    dataRendererOptions: {
                      unusedOptionalUrl: jsonurl
                    }
                  });

            });

        </script>

        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


