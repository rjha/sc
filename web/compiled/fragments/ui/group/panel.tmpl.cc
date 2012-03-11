 <div class="group-panel">
    <b>Groups</b>&nbsp;
    <div class="box"> 
        New Group 
        <input id="group-box" name="new-group" value="" />
        <button id="add-group-btn" type="button" class="btn" value="Add"><i class="icon-tags"> </i>&nbsp;Add</button> 
    </div>

    <div class="wrapper"> 
        <!-- first panel -->
        <div class="groups">
            <ul class="unstyled">
            <?php for($i = 0 ; $i < 2 ; $i++ ) { ?>
                <li> <input type="checkbox" name="g[]" value="<?php echo $view->records[$i]['value'] ?>" <?php echo $view->records[$i]['checked'] ?> /><?php echo $view->records[$i]['display'] ?></li>

            <?php } ?>
            </ul>
        </div>
            

        <!-- 2nd and 3rd visible panels -->
        <?php for($count = 2 ; $count < $view->numVisible ; $count = $count + $view->step ) { ?>
            <div class="groups">
                <ul class="unstyled">
                <?php for($i = $count ; (($i < $count+ $view->step) && ($i < $view->numVisible)) ; $i++) { ?>
                <li> <input type="checkbox" name="g[]" value="<?php echo $view->records[$i]['value'] ?>" <?php echo $view->records[$i]['checked'] ?>  /><?php echo $view->records[$i]['display'] ?></li>
                <?php } ?>
                 <?php if($view->moreLink) { ?>
                    <li> <a id="more-groups-link" href="#more-groups">more&rarr;</a> </li>

                <?php } ?>
                </ul>
            </div>
           <?php } ?> 
            
        
    </div>
</div> <!-- visible group panel -->

<div class="hide-me">
    <div id="more-groups" class="group-panel">
        <h3> More groups </h3>
        <hr>
        <div class="wrapper">
        <?php for($count = $view->numVisible ; $count < $view->total ; $count = $count + $view->step ) { ?>
            <div class="groups">
                <ul class="unstyled">
                <?php for($i = $count ; (($i < $count+ $view->step) && ($i < $view->total)) ; $i++) { ?>
                <li> <input type="checkbox" name="g[]" value="<?php echo $view->records[$i]['value'] ?>" <?php echo $view->records[$i]['checked'] ?>  /><?php echo $view->records[$i]['display'] ?></li>
                <?php } ?>
                </ul>
            </div>
           <?php } ?> 
        </div>
    </div> <!-- modal window groups panel -->

</div>

