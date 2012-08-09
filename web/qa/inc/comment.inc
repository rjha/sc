
<!-- include comment form -->
<div class="section">
    <?php echo $formErrors; ?>
        <form id="web-form1"  name="web-form1" action="/qa/form/comment.php?q=/item/<?php echo $itemId; ?>" enctype="multipart/form-data"  method="POST">

        <div class="error">  </div>

        <table class="form-table">
            <tr>
            <?php if(is_null($loginIdInSession)) { ?>
                <td> please <a href="<?php echo $loginUrl ?>">login</a> to comment</td>
            <?php } ?>

            </tr>
             <tr>
                <td>
                    <textarea  name="comment" class="required comment-box" title="Comment is required" cols="50" rows="4" ><?php echo $sticky->get('comment'); ?></textarea>
                </td>
             </tr>

        </table>

         <div>
            <button class="btn" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Comment</span></button>
        </div>

       <input type="hidden" name="owner_id" value="<?php echo $postDBRow['login_id']; ?>" />
       <input type="hidden" name="post_id" value="<?php echo $postDBRow['id']; ?>" />
       <input type="hidden" name="post_title" value="<?php echo $postDBRow['title']; ?>" />
       <input type="hidden" name="fUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />

    </form>
    </div> <!-- comment wrapper -->


