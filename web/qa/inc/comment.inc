<style type="text/css">
     /* @todo comment form - inline css - move to sc.css */
    .form-table {
        margin-bottom : 0px;
    }

    .form-table textarea {
        width:500px;
        height:60px;
    }
</style>
<!-- include comment form -->
<div id="comment-wrapper">
    <?php echo $formErrors; ?>
        <form id="web-form1"  name="web-form1" action="/qa/form/comment.php?q=/item/<?php echo $itemId; ?>" enctype="multipart/form-data"  method="POST">

        <div class="error">  </div>

        <table class="form-table">
            <tr>
            <?php if(is_null($loginId)) { ?>
                <td> please <a href="<?php echo $loginUrl ?>">login</a> to comment</td>
            <?php } ?>

            </tr>
             <tr>
                <td>
                    <textarea  name="comment" class="required comment-box" title="Comment is required" cols="50" rows="4" ><?php echo $sticky->get('comment'); ?></textarea>
                </td>
             </tr>

        </table>

         <div class="form-actions2">
            <button class="btn" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Add Comment</span></button>
        </div>

       <input type="hidden" name="owner_id" value="<?php echo $postDBRow['login_id']; ?>" />
       <input type="hidden" name="post_id" value="<?php echo $postDBRow['id']; ?>" />
       <input type="hidden" name="post_title" value="<?php echo $postDBRow['title']; ?>" />
       <input type="hidden" name="fUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />

    </form>
    </div> <!-- comment wrapper -->


