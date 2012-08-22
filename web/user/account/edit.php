<?php

    //sc/user/profile/edit.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use \com\indigloo\Util;
    use \com\indigloo\Url;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\sc\auth\Login as Login ;
    use \com\indigloo\sc\html\User as User;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
    $fUrl = Url::current();

    $gSessionLogin = Login::getLoginInSession();
    $loginId = $gSessionLogin->id ;

    $userDao = new \com\indigloo\sc\dao\User() ;
    $userDBRow = $userDao->getonLoginId($loginId);

    $emailExtra = ' readonly="readonly" ' ;
    if(strcmp($userDBRow['provider'],Login::TWITTER) == 0 ) {
        //allow editing
        $emailExtra = '' ;
    }

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Edit Profile - <?php echo $userDBRow['first_name']; ?>  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
       <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
            <div class="row">
                <div class="span8">
                    <div class="page-header">
                        <h2> <?php echo $userDBRow['name']; ?> </h2>
                    </div>

                    <p class="help-text">
                       Please update the details and click on Submit.  If you provide a nick name then your nick name
                       will be displayed instead of your real name.
                    </p>
                </div>
            </div> <!-- row -->

            <div class="row">
                <div class="span8">
                    <div class="wrapper" style="border-right:1px dotted #ccc;">

                    <?php FormMessage::render(); ?>

                        <form id="web-form1"  name="web-form1" action="/user/account/form/edit.php" enctype="multipart/form-data"  method="POST">

                            <div class="error">    </div>

                            <table class="form-table">

                                 <tr>
                                    <td class="field">First Name<span class="red-label">*</span></td>
                                    <td>
                                        <input type="text" name="first_name" maxlength="32" class="required" title="&nbsp;First Name is required" value="<?php echo $sticky->get('first_name',$userDBRow['first_name']); ?>"/>
                                    </td>
                                 </tr>
                                  <tr>
                                    <td class="field">Last Name<span class="red-label">*</span></td>
                                    <td>
                                        <input type="text" name="last_name" maxlength="32" class="required" title="&nbsp;Last Name is required" value="<?php echo $sticky->get('last_name',$userDBRow['last_name']); ?>"/>
                                    </td>
                                 </tr>
                                  <tr>
                                    <td class="field">Nick Name</td>
                                    <td>
                                        <input type="text" name="nick_name" maxlength="32" value="<?php echo $sticky->get('nick_name',$userDBRow['nick_name']); ?>"/>
                                    </td>
                                 </tr>
                                  <tr>
                                    <td class="field">Email<span class="red-label">*</span></td>
                                    <td>
                                    <input type="text" name="email" maxlength="64" class="required" title="&nbsp;Email is required" value="<?php echo $sticky->get('email',$userDBRow['email']); ?>" <?php echo $emailExtra; ?> />
                                    </td>
                                 </tr>
                                  <tr>
                                    <td class="field">Website</td>
                                    <td>
                                        <input type="text" name="website" maxlength="128" value="<?php echo $sticky->get('website',$userDBRow['website']); ?>"/>
                                    </td>
                                 </tr>
                                 <tr>
                                  <td class="field">Blog</td>
                                    <td>
                                        <input type="text" name="blog" maxlength="128" value="<?php echo $sticky->get('blog',$userDBRow['blog']); ?>"/>
                                    </td>
                                 </tr>
                                 <tr>
                                  <td class="field">Location</td>
                                    <td>
                                        <input type="text" name="location" maxlength="32" value="<?php echo $sticky->get('location',$userDBRow['location']); ?>"/>
                                    </td>
                                 </tr>
                                 <tr>
                                  <td class="field">Age</td>
                                    <td>
                                        <input type="text" name="age" maxlength="2" value="<?php echo $sticky->get('age',$userDBRow['age']); ?>"/>
                                    </td>
                                 </tr>
                                  <tr>
                                      <td class="field">&nbsp;</td>
                                      <td class="field">About me (512 characters)</td>
                                 </tr>

                                 <tr>
                                  <td class="field">&nbsp;</td>
                                   <td>
                                    <textarea  id="about_me" maxlength="512" name="about_me" class="h130" cols="2" rows="4" ><?php echo $sticky->get('about_me',$userDBRow['about_me']); ?></textarea>
                                    <br>
                                   <span id="about_me_counter"></span>
                                  </td>
                                </tr>
                            </table>

                            <div class="form-actions">
                                <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button>
                                <a href="<?php echo $qUrl;?>">
                                    <button class="btn" type="button" name="cancel"><span>Cancel</span></button>
                                </a>

                            </div>

                            <div style="clear: both;"></div>
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                            <input type="hidden" name="photo_url" value="<?php echo $userDBRow['photo_url']; ?>" />

                        </form>
                    </div> <!-- wrapper -->
                </div>
                <div class="span4">
                    <div id="my-photo">
                        <?php echo User::getPhoto($userDBRow['name'], $userDBRow['photo_url']); ?>
                    </div>
                    <div id="image-uploader"> </div>
                </div>

            </div> <!-- row -->
        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
                $("#web-form1").validate({errorLabelContainer: $("#web-form1 div.error")});
                webgloo.sc.util.addTextCounter("#about_me", "#about_me_counter");

                var uploader = new qq.FileUploader({

                    element: document.getElementById('image-uploader'),
                    action: '/upload/image.php',
                    debug: false,

                    labelOfButton : 'Upload Image',
                    allowedExtensions: ['png','gif','jpg','jpeg'],

                    onComplete: function(id, fileName, responseJSON) {
                        mediaVO = responseJSON.mediaVO;
                        var imageData = {};
                        imageData.name = mediaVO.originalName;
                        if(mediaVO.store == 's3'){
                            imageData.srcImage = 'http://' + mediaVO.bucket + '/' + mediaVO.storeName ;
                        } else {
                             imageData.srcImage = '/' + mediaVO.bucket + '/' + mediaVO.thumbnail ;
                        }

                        //update our display
                        imageDiv = '<div class="widget"> <div class="photo"> ' +
                                    ' <img src="{srcImage}" alt="{name}" /> </div> </div>' ;
                        var buffer = imageDiv.supplant(imageData);
                        $("#my-photo").html(buffer);
                        //add to form
                        frm = document.forms["web-form1"];
                        frm.photo_url.value = imageData.srcImage ;

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
