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
    use com\indigloo\sc\util\PseudoId as PseudoId;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));


    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
    $fUrl = Url::current();

    $encodedId = Url::getQueryParam("id");
    $commentId = PseudoId::decode($encodedId);

    $commentDao = new \com\indigloo\sc\dao\Comment();
    $commentDBRow = $commentDao->getOnId($commentId);

    if(!Login::isOwner($commentDBRow['login_id'])) {
        header("Location: /qa/noowner.php");
        exit ;
    }

?>

<!DOCTYPE html>
<html>

    <head>
        <title>3mik.com - Delete a comment</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
       <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh800">
           
            <div class="row">
                <div class="span9">


                    <div class="page-header">
                        <h2> Delete Comment</h2>
                    </div>
                    <div class="alert">
                      <a class="close" data-dismiss="alert">×</a>
                      Please make sure that you really want to delete this comment.
                    </div>

                    <?php FormMessage::render(); ?>
                    <?php echo \com\indigloo\sc\html\Comment::getWidget($commentDBRow); ?>

                    <form id="web-form1"  name="web-form1" action="/qa/comment/form/delete.php" method="POST">
                        <div>
                            <button class="btn btn-danger" type="submit" name="delete" value="Delete">Delete</button>
                            <a href="<?php echo $qUrl; ?>"><button class="btn" type="button">Cancel</a></button></a>
                        </div>
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        <input type="hidden" name="comment_id" value="<?php echo $commentId; ?>" />
                    </form>

                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.js"); ?>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
