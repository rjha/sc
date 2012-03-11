<div class="well">
	<?php echo $view->description ?>
	<br>
	<div class="tags"> </div>

	<div class="author">
		<img src="/nuke/twitter-icon.png" height="32" width="32" alt="icon" /> 		
		<div class="meta">
			<span class="b"> <a href="/pub/user/<?php echo $view->loginId ?>"><?php echo $view->userName ?></a></span>
			<br>
			<span class="b"><?php echo $view->createdOn ?></span>
		</div>	
	</div>
</div>

