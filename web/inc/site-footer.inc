
<div id="footer">
    All rights reserved (2012-2013)  3mik.com

</div>

<?php
    //free resources
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->end();

    // close redis connx.
    // do not create a new connx.
    $redis = \com\indigloo\connection\Redis::getInstance(false) ;
    if(!is_null($redis)) {
        $redis->close();
    }
    
?>
