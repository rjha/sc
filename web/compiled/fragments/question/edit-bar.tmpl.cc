<div class="ml40">
	<span> <a class="btn " href="#form-wrapper">Add Comment</a></span> 
    <?php if($view->isLoggedInUser){ ?>
	<span> <a class="btn btn-primary" href="/qa/edit.php?id=<?php echo $view->id ?>">Edit</a></span> 
    <?php } ?>
</div>

