<!-- include bootstrap carousel -->
<div id="myCarousel" class="carousel">
  <!-- Carousel items -->
  <div class="carousel-inner">
            <?php
                $strItem = '<div class="item {active}"> <img src="{srcImage}"></div>';
                $count = 0 ;
                
                foreach($images as $image) {
                    $active = ($count == 0) ? 'active' : '' ;
                    $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? "http://" : "/" ;
                    $srcImage = $prefix.$image->bucket."/".$image->storeName ;


                    $item = str_replace(array(0 => "{srcImage}", 1=>"{active}"),
                                        array(0 => $srcImage, 1=> $active),
                                        $strItem);
                    echo $item;
                    $count++ ;
                    
                }
            
            ?>
  </div>

  <?php if($count >1 ) { ?>
      <!-- Carousel nav -->
      <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
      <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
  <?php } ?>

</div> <!-- carousel -->

    <div class="row">
        <div class="mt20">
        <div class="span5">
            Total images&nbsp; <span class="badge badge-warning"> <?php echo $count; ?> </span>
        </div>

        <div class="span2">
            <div class="fb-like" data-href="<?php echo $pageUrl;?>" data-send="true" data-layout="button_count" data-width="220" data-show-faces="false"></div>
        </div>
        <div class="span1">
                    <a href="https://twitter.com/share" class="twitter-share-button" data-via="3mikindia" data-count="none">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>

        </div>
    </div> <!-- row -->

