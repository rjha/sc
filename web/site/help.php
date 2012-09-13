<?php

    include('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik - Help page </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container">
            
                <div class="row">
                    <div class="span6 offset2 mt20">
                        <h3> Looking for help? </h3>
                        <p> 
                            Look no further! The first place to start is our FAQ or frequently 
                            asked questions. If you can't find your answer there, please send 
                            us an email <a href="mailto:support@3mik.com">support@3mik.com</a>.
                            We will try our best to help. 
                            Other pages that you may be interested in:
                        </p>
                        <ul>
                            <li> <a href="/site/contact.php">contact page </a> </li>
                            <li> <a href="/site/copyright.php">copyright page </a> </li>
                        </ul>

                    </div>
            </div> <!-- row -->


            <div class="row">
                <div id="faq" class="span8 offset2 mt20">
                    <h3> Frequently asked questions </h3>
                    <ul class="unstyled">
                        <li> <a href="#q1">How can I share 3mik items with the world?</a></li>
                        <li> <a href="#q2">How can I view more items from a shop or user? </a></li>
                        <li> <a href="#q1">How can I share my items on 3mik? </a></li>
                        <li> <a href="#q4">How do I login (Sign In)? </a></li>
                        <li> <a href="#q5">How can I bookmark my favorite items on 3mik to view later? </a></li>
                        <li> <a href="#q6">How do I access my 3mik account page? </a></li>
                        <li> <a href="#q7">How can I edit/delete my items and comments? </a></li>
                       
                    </ul>
                    <div  class="widget p10">
                        <h4> what is 3mik?</h4>
                        <div class="description">
                             3mik is a sharing and discovery platform. 
                             3mik lets you share interesting and unique 
                             things in India. You can use 3mik to discover 
                             your interests and see items shared by others. 
                             To learn more please 
                             <a href="/site/about.php" target="_blank">check the about 3mik page</a>

                        </div>
                        
                    </div>

                    <div id="q1" class="widget p10">
                        <h4> How can I share 3mik items with the world?</h4>
                        <div class="photo">
                            <img src="/site/images/help/item-page.png" />
                        </div>
                        <p>
                            when you are on an item page , look at the right hand side of page.
                            You will use the Facebook like button or g+ or tweet button to share
                            your item with your friends. You can also send the 3mik item link in email.
                        </p>
                        <div class="photo">
                            <img src="/site/images/help/item-page-actions.png" />
                        </div>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>
                    <div id="q2" class="widget p10">
                        <h4> How can I view more items from a shop or user?</h4>
                        <div class="photo">
                            <img src="/site/images/help/item-page-more.png" />
                        </div>
                        <p>
                        From home page or search results, please click on item image to go to
                        item details page. When you are on item details page, just scroll down
                        past the item images and description. You will see links for more items from
                        same shop and more items from the user who posted this item.
                        </p>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                     <div id="q3" class="widget p10">
                        <h4> How can I share my items on 3mik?</h4>
                        
                        <div class="photo">
                            <img src="/site/images/help/3mik-top-bar.png" />
                        </div>

                        <p>
                            To share items on 3mik, just click on the 
                            Add+/upload button in toolbar. You can select to 
                            upload images from your computer 
                            or you can share images from a webpage. 
                        </p>

                         <div class="photo">
                            <img src="/site/images/help/share-choice.png" />
                        </div>
                        <p>
                            To upload images from your computer, click on Add images button in 
                            the next screen. Browse to the folder where you have stored your images. 
                            You can select multiple images.
                        </p>
                        <p>
                            Share a webpage by entering a URL and selecting images that you wish 
                            to share. Enter a URL and click on Fetch. The page would show images, 
                            you can select the appropriate ones and click on "Next". 

                            You can select multiple images. Note that some images may not appear 
                            if they are not of a suitable size. The submitted images will appear 
                            at the bottom of the form on next screen.
                        </p>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>
                    <div id="q4" class="widget p10">
                        <h4> How do I login?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-top-bar.png" />
                        </div>

                        <p>
                            Click the "Join now" button at the top of the page. You can sign 
                            in using an existing Facebook, Google or Twitter account.
                            You can also create a 3mik account using your email. 
                            Registration is simple and takes only a minute. 
                            
                        </p>

                        <div class="photo">
                            <img src="/site/images/help/3mik-login.png" />
                        </div>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                   
                     <div id="q5" class="widget p10">
                        <h4>How can I bookmark my favorite items on 3mik to view later?</h4>
                        <div class="photo">
                            <img src="/site/images/help/home-like.png" />
                        </div>
                        <p>
                           You can click the favorite button on an item on home page 
                           and search results. You can also favorite the item on item 
                           details page. You can view these items any time from your 3mik
                           account page. 
                        </p>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>
                    <div id="q6" class="widget p10">
                        <h4> How do I access my 3mik account page?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-user-menu.png" />
                        </div>
                        <p>
                           To access your 3mik account, you should login first. The site toolbar
                           will then show an entry with your name. Clicking your name will show
                           links to your posts, comments, settings, profile and other pages.
                        </p>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>
                     <div id="q7" class="widget p10">
                        <h4> How can I edit/delete my items and comments?</h4>
                       
                        <p>
                           To edit or delete your items or comments, you should login first. 
                           Then go to your posts or comments page. There you will see options to
                           edit and delete. 
                           <br>
                           Item page also shows an edit button when you are logged in.
                        </p>
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>
                   
                </div> <!-- span8 -->

            </div> <!-- row -->

        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(function(){
                $("#question1").collapse('show');
                webgloo.sc.toolbar.add();

            });
        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
