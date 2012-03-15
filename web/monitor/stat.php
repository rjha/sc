<?php
    $postDao = new \com\indigloo\sc\dao\Post();
    $postsCount = $postDao->getTotalCount();

    $filter = array($postDao::DATE_COLUMN => "24 HOUR");
    $ldPostsCount = $postDao->getTotalCount($filter); 

    $commentDao = new \com\indigloo\sc\dao\Comment();
    $commentsCount = $commentDao->getTotalCount();



?>

<ol>
<li> Total Posts : <?php echo $postsCount; ?> </li>
    <li> Total Comments :<?php echo $commentsCount; ?>  </li>
    <li> Total Users : </li>
    <li> Total Groups : </li>
    <li> Posts in last 24 HR : <?php echo $ldPostsCount; ?>   </li>
    <li> Users in last 24 HR : </li>
</ol>
