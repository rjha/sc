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

    if( (strlen($gtoken) > 5) && (strcmp(substr($gtoken,0,5), "user:") == 0)){
        $ft = "user" ;
        $userId = substr($gtoken,5);
        //reset search token
        $gtoken = NULL ;
    }

    if(!empty($gtoken)) {
        $ft = "name" ;
    }

    if(!is_null($ft)) {
        switch($ft){
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
    $paginator = new \com\indigloo\ui\Pagination($qparams, $total, $pageSize);
    $userDBRows = $userDao->getPaged($paginator,$filters);
    $gtoken = "" ;
    

    //past 24 hour filter
    $filters = array();
    $model = new \com\indigloo\sc\model\User();
    $filter = new Filter($model);
    $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
    array_push($filters,$filter);
    $l24hTotal = $userDao->getTotal($filters);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - users in system</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        
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
                <div class="span9">

                     <div class="row">
                        <div class="span4">
                            <form method="GET" action="<?php echo $clearSearchUrl; ?>">
                                <input id="site-search" name="gt" type="text" class="search-query" placeholder="Search...">
                                <input type="hidden" name="ft" value="<?php echo $ft; ?>"/>
                            </form>

                        </div>

                        <div class="span4">
                            <div class="faded-text">
                                <ul class="unstyled">
                                    <li> type user:&lt;user_id&gt;, e.g. user:9293 for a single user </li>
                                    
                                </ul> 
                            </div>
                           
                        </div>

                    </div> <!-- row -->

                    <div class="p10">
                        <span class="label label-warning"> Total: <?php echo $total; ?> </span>
                        &nbsp;
                        <span class="label label-warning"> Last 24 HR <?php echo $l24hTotal; ?> </span>
                        &nbsp;
                        <span class="color-red">
                            filters (<?php echo $ftname; ?>)
                        </span>
                        &nbsp;
                        <a href="/monitor/users.php">All Users</a>

                    </div>
 
                    <div class="mt20">
                         <?php
                                $startId = NULL;
                                $endId = NULL;

                                if (sizeof($userDBRows) > 0) {
                                    $startId = $userDBRows[0]['id'];
                                    $endId = $userDBRows[sizeof($userDBRows) - 1]['id'];
                                }

                                foreach ($userDBRows as $userDBRow) {
                                    echo \com\indigloo\sc\html\User::getWidget($userDBRow);
                                }

                                
                            ?>
                    </div>
                </div>
                 
            </div>
        </div> <!-- container -->
        <?php $paginator->render('/monitor/users.php', $startId, $endId); ?>


        <div id="ft">
        <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


