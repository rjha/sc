<?php

    //sc/share/new.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util;
    use \com\indigloo\Url;
    use \com\indigloo\ui\form\Sticky;
    
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = Url::current();

    $strImagesJson = $sticky->get('images_json') ;
    $strLinksJson = $sticky->get('links_json') ;

    $strImagesJson = empty($strImagesJson) ? '[]' : $strImagesJson ;
    $strLinksJson = empty($strLinksJson) ? '[]' : $strLinksJson ;

    $loginId = Login::tryLoginIdInSession() ;

    //add security token to form
    $formToken = Util::getBase36GUID();
    $gWeb->store("form.token",$formToken);



?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Share your find, need and knowledge</title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>
 
    </head>

    <body>
        <style>
            /* @inpage @hardcoded remove hack */
            .form-table {width:90%;}

        </style>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span12">
                     <div class="page-header">
                        <h3> Share</h3> 
                    </div>
                   
                </div>
            </div>
            <div class="row">
                <div class="span12">
                     <?php FormMessage::render(); ?>
                </div>

                <div class="span6">
                   
                    <form  id="web-form1"  name="web-form1" action="/qa/form/new.php" enctype="multipart/form-data"  method="POST">
                      
                        <table class="form-table">
                            <tr>
                                <td> <label>Category</label>

                                <?php
                                    $options = array('name' => 'category', 'empty' => true);
                                    $collectionDao = new \com\indigloo\sc\dao\Collection();
                                    $catRows = $collectionDao->uizmembers(\com\indigloo\sc\util\Nest::ui_category());
                                    echo \com\indigloo\ui\SelectBox::render($catRows,$options);
                                  ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label>Details*&nbsp;(max 512 chars)</label>
                                    <textarea  id="description" maxlength="512" name="description" class="required h130 wp100" cols="50" rows="4" ><?php echo $sticky->get('description'); ?></textarea>
                                    <br>
                                   <span id="description_counter"></span>
                                </td>
                            </tr>
                            <tr>
                                <td> <label>Groups(separate groups using comma)</label>
                                <input type="text" class="wp100" name="group_names" maxlength="64" value="<?php echo $sticky->get('group_names'); ?>" />
                                <br>
                                <span id="group_preview"></span>
                            </tr>

                            <tr>
                                <td>
                                    <label>Website</label>
                                    <input type="text" class="wp100" id="link-box" name="link" value="<?php echo $sticky->get('link'); ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div>
                                        <span class="faded-text">
                                            Note: by posting your content, you agree to abide by 
                                            the 3mik <a href="/site/tos.php" target="_blank">terms of service</a> 
                                            and
                                           &nbsp;<a href="/site/privacy.php" target="_blank">privacy policy</a>
                                        </span>
                                                                
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit" name="save" value="Save"><span>Submit</span></button>
                                        <a href="<?php echo base64_decode($qUrl); ?>"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

                                </td>
                               
                            </tr>

                        </table>

                        
                        <!-- put json data in single quotes to avoid interpreting double quotes -->
                        <input type="hidden" name="links_json" value='<?php echo $strLinksJson ; ?>' />
                        <input type="hidden" name="images_json" value='<?php echo $strImagesJson ; ?>' />
                        <input type="hidden" name="token" value="<?php echo $formToken; ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>



                </div> <!-- col:1 -->
                <div class="span6">
                    <div id="ful-message"> </div>
                     <div class="row">
                            <div class="span6"><div id="image-uploader"> </div></div>
                        </div>
                        
                        <div class="section1">
                             <div id="image-preview"> </div>
                            
                        </div>
                        <div class="section1">
                            <div id="link-preview"> </div>
                        </div>
                       
                       

                </div> <!-- col:2 -->

            </div>

        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                $("#web-form1").validate({
                       errorLabelContainer: $("#web-form1 div.error")
                });

                webgloo.media.init(["image", "link"]);
                webgloo.media.attachEvents();
                webgloo.sc.util.addTextCounter("#description", "#description_counter");

                //@imp: we pass our own button label to the fileupload js
                var uploader = new qq.FileUploader({
                    element: document.getElementById('image-uploader'),
                    action: '/upload/image.php', 
                    allowedExtensions: ['png','gif','jpg','jpeg'],
                    debug: false,
                    uploadButtonText : 'Add photo',

                    onComplete: function(id, fileName, responseJSON) {
                        webgloo.media.addImage(responseJSON.mediaVO);
                    },

                    showMessage: function(message){ 
                        var tmpl = '<li class="qq-uplad-fail"> <span class="error"> {message}</span></li> ';
                        var errorMessage = tmpl.supplant({"message" : message}) ;
                        $(".qq-upload-list").append(errorMessage);
                        
                    }
                });

            });

        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
