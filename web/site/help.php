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
            <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
           
                <div class="row">
                    <div class="span12">
                        <div class="page-header"> <h3> Looking for help?</h3></div>
                    </div>

                </div>

                <div class="row">
                    <div class="span5 offset1">
                       
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
                    <div class="span5">
                        <div class="ml20">
                            <h4> Follow 3mik on</h4>
                            <a href="http://www.facebook.com/3mikindia" target="_blank">
                                <div class="floatl p10">
                                    <img src="/css/asset/sc/ico_facebook.png"/>
                                    <br>
                                    Facebook
                                </div>
                            </a>
                            <a href="https://plus.google.com/108668124722035129135" target="_blank">
                                <div class="floatl p10">
                                    <img src="/css/asset/sc/ico_googleplus.png"/>
                                    <br>
                                    Google+
                                </div>
                            </a>

                            <a href="http://www.twitter.com/3mikindia" target="_blank">
                                <div class="floatl p10">
                                    <img src="/css/asset/sc/ico_twitter.png"/>
                                    <br>
                                    Twitter
                                </div>
                            </a>

                        </div> <!-- social:buttons -->
                    </div>
            </div> <!-- row -->
          

            <div class="row">
                <div id="faq" class="span6 offset2 mt20">
                    <h3> Frequently asked questions </h3>
                    <ol>
                        <li> <a href="#q1">How can I register / create a new account?</a></li>
                        <li> <a href="#q2">How can I Sign in / access my account?</a></li>
                        <li> <a href="#q3">I want to share. How can I add my items? </a></li>
                        <li> <a href="#q4">How can I view more items from a shop?</a></li>
                        
                        <li> <a href="#q5">How can I view more items from a user?</a></li>
                        <li> <a href="#q6">How can I bookmark my favorite items on 3mik to view later?</a></li>
                        <li> <a href="#q7">How can I tell my friends about my 3mik items?</a></li>
                        <li> <a href="#q8">How can I edit/delete my stuff?</a></li>
                       
                    </ol>
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
                        <h4> How can I register / create a new account?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-join.png" />
                        </div>
                        <ul>
                           <li> Click on Login/Registration in the top toolbar </li>
                           <li> or click the red Join 3mik button at the top of page. </li>
                           
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                    <div id="q2" class="widget p10">
                        <h4> How can I Sign in / access my account?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-login.png" />
                        </div>
                        <ul>
                           <li> Click on Login/Registratio in the top toolbar </li>
                           <li> After sign in, click on your name in top toolbar</li>
                            <li>Select Account</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                    <div id="q3" class="widget p10">
                        <h4>I want to share. How can I add my items?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-upload.png" />
                        </div>
                        <ul>
                           <li> Click on Upload button at the top of page</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                     <div id="q4" class="widget p10">
                        <h4>How can I view more items from a shop?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-shop-link.png" />
                        </div>
                        <ul>
                            <li>Go to item details page (by clicking on item image)</li>
                            <li>After the item images, you will see website link for shop (if available)</li>
                            <li>Click on shop link</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                    <div id="q5" class="widget p10">
                        <h4>How can I view more items from a user?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-user-link.png" />
                        </div>
                        <ul>
                            <li>Go to item details page (by clicking on item image)</li>
                            <li>you will see user name at the top right of page</li>
                            <li>Click on user name link</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                    <div id="q6" class="widget p10">
                        <h4>How can I bookmark my favorite items on 3mik to view later?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-bookmark.png" />
                        </div>
                        <ul>
                            <li>Click on save button on top of an item image</li>
                            <li>Or click on Save link on item details page</li>
                            <li>you can save this item to an existing list or create a new list</li>
                            <li>You can view your lists from your public page or account page</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                    <div id="q7" class="widget p10">
                        <h4>How can I tell my friends about my 3mik items?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-social-sharing.png" />
                        </div>
                        <ul>
                            <li>You can send your public page link</li>
                            <li>Share 3mik items via Facebook/Google+/Twitter</li>
                            
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>

                      <div id="q8" class="widget p10">
                        <h4>How can I edit/delete my stuff?</h4>
                        <div class="photo">
                            <img src="/site/images/help/3mik-edit.png" />
                        </div>
                        <ul>
                            <li>Sign in</li>
                            <li>After sign in, you will be taken to your account page</li>
                            <li>Click on on of the acocunt menu link</li>
                            <li>Place your mouse over an item to get edit/delete options</li>
                        </ul>
                        
                        <div class="floatr p10"> <a href="#faq">Back to top&nbsp;&uarr;</a></div>
                    </div>


                </div> <!-- span:wrapper -->

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
