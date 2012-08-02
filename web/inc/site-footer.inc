
<div id="footer">
    All rights reserved (2012-2013)  3mik.com  </div>

</div>

<?php
    //free resources
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->end();

    // close redis connx.
    // do not create a new connx.
    $redis = \com\indigloo\sc\util\Redis::getInstance(false) ;
    if(!is_null($redis)) {
        $redis->close();
    }
    
?>

<!-- google analytics -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30176357-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
