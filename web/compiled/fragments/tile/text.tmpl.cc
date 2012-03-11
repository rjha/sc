
<div class="tile">
   <div class="options">
        <div class="links">
            <a href="#">Like&nbsp;<i class="icon-heart"></i></a>&nbsp;
            <a href="/item/<?php echo $view->id ?>">Comment&nbsp;<i class="icon-comment"></i></a>&nbsp;
        </div>
    </div>
   <div class="description">
	 <?php echo $view->description ?>
   </div>
 	   
	<div class="author">
		<div class="meta">
			<span class="b"> <a href="<?php echo $view->userPageURI ?>"><?php echo $view->userName ?></a> </span>
			<span>&nbsp;<?php echo $view->createdOn ?></span>
		</div>
	</div>

    <?php if($view->hasGroups) { ?>
    <div class="groups">
    Groups:
        <?php foreach($view->groups as $group){ ?>
            <a href="/group/<?php echo $group['slug'] ?>"><?php echo $group['display'] ?></a>&nbsp;

        <?php } ?>

    </div>
    <?php } ?>


	<div><a href="/item/<?php echo $view->id ?>">more&nbsp;&#8594;</a></div>
</div>

