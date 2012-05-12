<?php

    //sc/monitor/users.php
    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\ui\Filter as Filter;
    
    $loginDao = new \com\indigloo\sc\dao\Login();
    
    //past 24 hour filter
    $filters = array();
    $model = new \com\indigloo\sc\model\Login();
    $filter = new Filter($model);
    $filter->add($model::CREATED_ON,Filter::GT,"24 HOUR");
    array_push($filters,$filter);

    $ldLoginCount = $loginDao->getTotalCount($filters); 
    $loginCount = $loginDao->getTotalCount(); 
    $logins = $loginDao->getLatest(5);

?>


<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - feedback posted by users  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        
        <script>
            $(document).ready(function(){
               
            });
            
        </script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR'] . '/monitor/inc/toolbar.inc'); ?>
                </div> 

            </div>

            <div class="row">
                <div class="span12">
                <?php include($_SERVER['APP_WEB_DIR']. '/monitor/inc/banner.inc'); ?>
                </div>
            </div>

            <div class="row">
                <div class="span9">
                    <div class="page-header"> <h2> <?php echo $loginCount ?> Users </h2> </div>
                    
                    <ol>
                        <li> Users in last 24 HR : <?php echo $ldLoginCount; ?> </li>
                    </ol>

                    <h3> Latest Users </h3>
                    <?php echo \com\indigloo\sc\html\Login::getList($logins); ?>

                   
                </div>
                <div class="span3">
                     <?php include($_SERVER['APP_WEB_DIR'].'/monitor/inc/menu.inc'); ?>
                </div>
            </div>
        </div> <!-- container -->
        

        <div id="ft">
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>


