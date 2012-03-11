<div class="widget bbd5">
	<div class="row">
		<div class="span7">

			<div class="photo">
				<a href="/item/<?php echo $view->id ?>"> <img src="<?php echo $view->srcImage ?>" class="alignleft" width="<?php echo $view->width ?>" height="<?php echo $view->height ?>" title="<?php echo $view->originalName ?>"  alt="<?php echo $view->originalName ?>"/> </a> 

			</div>
			<div class="description">
				<?php echo $view->description ?>
			</div>

		</div> <!-- span7-->
		<div class="span2">

			<div class="author">
				<img src="/nuke/twitter-icon.png" height="32" width="32" alt="icon" /> 		
				<div class="meta">
					<span class="b"><?php echo $view->userName ?></span>
					<br>
					<span class="b"><?php echo $view->createdOn ?> </span>
				</div>	
				<div> <a href="/item/<?php echo $view->id ?>">more&rarr;</a></div>
			</div>
			<?php if($view->isLoggedInUser) { ?>
			<div class="btn-group">
				<a data-toggle="dropdown" href="#">Actions<span class="caret"></span> </a>
				  <ul class="dropdown-menu">
					<li> <a href="/qa/edit.php?id=<?php echo $view->id ?>">Edit</a></li>
					<li> <a href="/qa/delete.php?id=<?php echo $view->id ?>">Delete</a> </li>
					
				  </ul>
			</div>	
			<?php } ?>
		</div> <!-- span2-->
	</div> <!-- row -->
</div>
