<?php
    $postDao = new \com\indigloo\sc\dao\Post();
    $postsCount = $postDao->getTotalCount();

    $filter = array($postDao::DATE_COLUMN => "24 HOUR");
    $ldPostsCount = $postDao->getTotalCount($filter); 

    $commentDao = new \com\indigloo\sc\dao\Comment();
    $commentsCount = $commentDao->getTotalCount();

    $loginDao = new \com\indigloo\sc\dao\Login();
    $filter = array($loginDao::DATE_COLUMN => "24 HOUR");
    $ldLoginCount = $loginDao->getTotalCount($filter); 
    $loginCount = $loginDao->getTotalCount(); 


?>

<ol>
<li> Total Posts : <?php echo $postsCount; ?> </li>
    <li> Total Comments :<?php echo $commentsCount; ?>  </li>
    <li> Total Users : <?php echo $loginCount; ?> </li>
    <li> Total Groups : </li>
    <li> Posts in last 24 HR : <?php echo $ldPostsCount; ?>   </li>
    <li> Users in last 24 HR : <?php echo $ldLoginCount; ?> </li>
</ol>
