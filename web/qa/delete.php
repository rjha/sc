<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Url as Url ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\sc\auth\Login as Login ;

    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    //q is part of URL and base64 encoded
    $qUrl = Url::tryBase64QueryParam("q","/");
    $fUrl = Url::current();

    $itemId = Url::getQueryParam("id");
    $postId = PseudoId::decode($itemId);

    $postDao = new \com\indigloo\sc\dao\Post();
    $postDBRow = $postDao->getOnId($postId);

    if(! (Login::isOwner($postDBRow['login_id']) || Login::isAdmin())) {
        header("Location: /site/error/403.html");
        exit ;
    }

?>

<!DOCTYPE html>
<html>

       <head>
        <title>3mik.com - Delete a post</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
            
            <div class="row">
                <div class="span9">


                    <div class="page-header">
                        <h2> Delete Post</h2>
                    </div>
                    <div class="alert">
                      <a class="close" data-dismiss="alert">×</a>
                      <strong>Warning!</strong> There is no way to recover a deleted post.
                      Please make sure that you really want to delete this post.
                    </div>

                    <?php FormMessage::render(); ?>
                    <?php echo \com\indigloo\sc\html\Post::getWidget($postDBRow); ?>
                    <div class="p10">&nbsp;</div>
                    <form id="web-form1"  name="web-form1" action="/qa/form/delete.php" method="POST">
                        <div>
                            <button class="btn btn-danger" type="submit" name="delete" value="Delete">Delete</button>
                            <a href="<?php echo base64_decode($qUrl); ?>"><button class="btn" type="button">Cancel</a></button></a>
                        </div>
                        <input type="hidden" name="q" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="post_id" value="<?php echo $postId; ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                    </form>

                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
