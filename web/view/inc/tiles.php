<script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>
	    
<script type="text/javascript">
    /* column width = css width + margin */
    $(document).ready(function(){
        var $container = $('#tiles');
        $container.imagesLoaded(function(){
            $container.masonry({
                itemSelector : '.tile'
                
            });
        });

    });
</script>

<div id="tiles">
    <?php
        foreach($questionDBRows as $questionDBRow) {
            $html = \com\indigloo\sc\html\Question::getTile($questionDBRow);
            echo $html ;
    
        }
    ?>
       
</div><!-- tiles -->


