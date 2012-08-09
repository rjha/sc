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

    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
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

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?>
        <link rel="stylesheet" type="text/css" href="/3p/ful/valums/fileuploader.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/ful/valums/fileuploader.js" ></script>

        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>

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
                    debug: false,
                    labelOfButton : 'Add Images',
                    onComplete: function(id, fileName, responseJSON) {
                        webgloo.media.addImage(responseJSON.mediaVO);
                    }
                });

            });

        </script>

    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/slim-toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span9">


                    <div class="page-header">
                        <h2> Share</h2>
                    </div>

                    <?php FormMessage::render(); ?>

                    <form  id="web-form1"  name="web-form1" action="/qa/form/new.php" enctype="multipart/form-data"  method="POST">
                        <div class="row">
                            <div class="span9"><div id="image-uploader"> </div></div>
                        </div>
                        <p> images will appear at the end of this form </p>
                        <table class="form-table">
                            <tr>
                                <td> <label>Category</label>

                                <?php
                                    $options = array('name' => 'category', 'empty' => true);
                                    $selectBoxDao = new \com\indigloo\sc\dao\SelectBox();
                                    $catRows = $selectBoxDao->get('CATEGORY');
                                    echo \com\indigloo\ui\SelectBox::render($catRows,$options);
                                  ?>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label>Details*&nbsp;(max 512 chars)</label>
                                    <textarea  id="description" maxlength="512" name="description" class="required h130 w500" cols="50" rows="4" ><?php echo $sticky->get('description'); ?></textarea>
                                    <br>
                                   <span id="description_counter"></span>
                                </td>
                            </tr>
                            <tr>
                                <td> <label>Groups (Separate groups using comma)</label>
                                <input type="text" name="group_names" maxlength="64" value="<?php echo $sticky->get('group_names'); ?>" />

                            </tr>

                            <tr>
                                <td>
                                    <label>Website (Type website and click Add or press Enter) </label>
                                    <input id="link-box" name="link" value="<?php echo $sticky->get('link'); ?>" />
                                    <button id="add-link" type="button" class="btn gBtnUp" value="Add"><i class="icon-plus-sign"> </i>&nbsp;Add</button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button>
                                        <a href="<?php echo $qUrl; ?>"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

                                </td>
                            </tr>

                        </table>

                        <div id="link-preview"> </div>
                        <div id="image-preview"> </div>

                        <!-- put json data in single quotes to avoid interpreting double quotes -->
                        <input type="hidden" name="links_json" value='<?php echo $strLinksJson ; ?>' />
                        <input type="hidden" name="images_json" value='<?php echo $strImagesJson ; ?>' />
                        <input type="hidden" name="token" value="<?php echo $formToken; ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>



                </div> <!-- span9 -->

                <div class="span3">
                    <?php include('sidebar/new.inc'); ?>
                </div>

            </div>

        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
