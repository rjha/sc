<?php

    use com\indigloo\ui\form\Message as FormMessage;

    $postDao = new \com\indigloo\sc\dao\Post();
    $filter = array($postDao::FEATURE_COLUMN => 1);
    $postDBRows = $postDao->getPosts($filter,50);

    $groupDao = new \com\indigloo\sc\dao\Group();
    $slug = $groupDao->getFeatureSlug();


?>

    <script type="text/javascript" src="/js/sc.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            webgloo.sc.groups.addPanelEvents();
        });
    </script>

        <?php FormMessage::render(); ?>
        <form name="web-form1" action="/monitor/form/feature/group.php" method="POST">
            <div class="row">
                <div class="span12">
                    <?php echo \com\indigloo\sc\html\GroupPanel::render($slug); ?> 
                </div>
             </div>
            <div class="form-actions"> 
                <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button> 
                <a href="/monitor"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
            </div>
            
            <input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"]; ?>" />
        </form>



<?php
    $template = $_SERVER['APP_WEB_DIR']. '/view/inc/tiles.php';
    include($template); 
?>

