<?php

    include('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Help about 3mik </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 
        <style>
            .accordion-inner img {
                padding:10px;
                border:6px solid #ccc ;
            }       


        </style>

    </head>

    <body>
        <div class="container mh600">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span11">
                <div class="page-header"> <h2> Help about 3mik </h2> </div> 
                    <div class="accordion" id="accordion2">
                        <!-- items -->
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question1">what is 3mik?</a>
                            </div>
                            <div id="question1" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div class="p20 comment-text">
                                    3mik is a sharing and discovery platform. 3mik lets you share interesting and unique things in India. You can use 3mik to discover your interests and see items shared by others. To learn more please <a href="/site/about.php" target="_blank">click this link</a>

                                    </div>
                                </div>
                            </div> 
                        </div> <!-- item:1 -->

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question2">How can I share an item on 3mik?</a>
                            </div>
                            <div id="question2" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/share-link.png" alt="share link" /> </div>
                                    <div class="p20 comment-text">
                                    To share items on 3mik, just click on the share link in toolbar at top of page. You can select to upload images from your computer or you can share images from a webpage. To upload images from your computer, click on Add images button in the next screen. Browse to the folder where you have stored your images. You can select multiple images.

                                    <br>
                                    <br>
Share a webpage by entering a URL and selecting images that you wish to share. Enter a URL and click on Fetch. The page would show images, you can select the appropriate ones and click on "Next". You can select multiple images. Note that some images may not appear if they are not of a suitable size.  The submitted images will appear at the bottom of the form on next screen.

                                    </div>
                                </div>
                            </div> 
                        </div> <!-- item:2 -->
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question3">How can I edit what I have shared?</a>
                            </div>
                            <div id="question3" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/item-edit-save.png" alt="item edit" /> </div>
                                    <div class="p20 comment-text">
                                Editing an item requires login. Please login into 3mik. You can go to the item page and click on the "Edit item" link. You can also click on your name in the toolbar and select Account  | Posts. There you can select a post to edit or delete it. 
                                    </div>
                                    <div> <img src="/site/images/help/toolbar-account.png" alt="toolbar account" /> </div>
                                </div>
                            </div> 
                        </div> <!-- item:3 -->
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question4">How can I share my 3mik items with my friends?</a>
                            </div>
                            <div id="question4" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/item-share.png" alt="item share" /> </div>
                                    <div class="p20 comment-text">
                                Please login into 3mik. You can go to the item page and click on share links. We support
sharing your items on Facebook and Google+.
                                    </div>
                                </div>
                            </div> 
                        </div> <!-- item:4 -->

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question5">How can I save the items I like on 3mik?</a>
                            </div>
                            <div id="question5" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/item-save.png" alt="item save" /> </div>
                                    <div class="p20 comment-text">
                                You can click the favorite button on an item on home page and search results. You can also favorite the item on item details page. All the items you favorite will appear on your account page.  
                                    </div>
                                </div>
                            </div> 
                        </div> <!-- item:5 -->

                         <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question6">How can I open my acocunt page to see my posts, comments, favorites and followers?</a>
                            </div>
                            <div id="question6" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/toolbar-account.png" alt="toolbar account" /> </div>
                                    <div class="p20 comment-text">
                                    Please login into 3mik. The toolbar will show you a link with your name. Please click your name in toolbar and select Account. Your account page showing your posts, comments and followers will open. You can edit or delete your posts on your account page.  
                                     </div>
                                </div>
                            </div> 
                        </div> <!-- item:6 -->

                         <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#question7">How can I create a 3mik account?</a>
                            </div>
                            <div id="question7" class="accordion-body collapse" style="height: 0px; ">
                                <div class="accordion-inner">
                                    <div> <img src="/site/images/help/login-page.png" alt="login page" /> </div>
                                    <div class="p20 comment-text">
                                        To create a 3mik account click on <a href="http://www.3mik.com/user/register.php">register</a>. Register link is also available in toolbar at the top of page.

                                You can also login using an existing Facebook, Google or Twitter account.
                                     </div>
                                </div>
                            </div> 
                        </div> <!-- item:7 -->

                    </div> <!-- accordion -->
                </div>

            </div> <!-- row -->

        </div> <!-- container -->

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?>

        <script type="text/javascript">
            $(function(){
                $("#question1").collapse('show');
                webgloo.sc.toolbar.add();

            });
        </script>



    </body>
</html>
