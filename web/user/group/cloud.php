<?php

    include ('sc-app.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
    include($_SERVER['APP_WEB_DIR'] . '/inc/role/user.inc');

    use \com\indigloo\sc\auth\Login as Login ;

    $groupDao = new \com\indigloo\sc\dao\Group();
    $loginId = Login::getLoginIdInSession();
    $groups = $groupDao->getOnLoginId($loginId);
?>

<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/sc.css">
</head>
<body>

<div class="cloud">
    <?php 

        if(isset($groups) && !empty($groups)) {
            foreach($groups as $group) {
                $num = rand(1,3000);
                $style = 1 ;
                if($num > 1000 ) { $style = 2 ; }
                if($num > 2000 ) { $style = 3 ; }
                echo \com\indigloo\sc\html\Group::getCloudLink($group,$style,false); 
            }
        }
    ?>
</div>
</body>
</html>

