<?php

    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

?>

<?php FormMessage::render(); ?>
					
<form  id="web-form1"  name="web-form1" action="/monitor/form/post/action.php" method="POST">
    <table class="form-table">
        <tr>
            <td> <label>Post IDS*</label> </td>
            <td> <input name="ids" type="text" value="<?php echo $sticky->get('ids'); ?>"/> </td>
        </tr>
        <tr>
            <td> <label>&nbsp;</label> </td>
            <td> separate multiple ids by comma</td>
        </tr>
        <tr>
            <td><label>Action</label> </td>
            <td>
                <select name="action">
                    <option value="">(select)</option>
                    <option value="delete">Delete</option>
                    <option value="add-feature">Feature</option>
                    <option value="remove-feature">Remove from Feature</option>

                </select>
            
            </td>
        </tr>
        <tr>
            <td> &nbsp; </td>
            <td> 
                <div class="form-actions"> 
                    <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Save</span></button> 
                    <a href="/monitor"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                </div>

            </td>
        </tr>
    </table>
    <input type="hidden" name="q" value="<?php echo $_SERVER["REQUEST_URI"]; ?>" />
</form>

