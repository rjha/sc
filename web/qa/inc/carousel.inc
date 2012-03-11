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

<div class="mt20 p20"> Total Images <b> <?php echo $count; ?> </b> </div>

