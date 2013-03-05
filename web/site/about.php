<?php
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> 3mik.com - Learn more  </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">
             <?php include(APP_WEB_DIR . '/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span4 offset1 mt20">
                    <h1> 3mik is a sharing and discovery platform</h1>
                    <blockquote class="pull-right">
                        <p>
                            3mik lets you share shops and products in India. 
                            You can use 3mik to discover your interests and 
                            share stuff that you like with others. Start exploring!
                        </p>
                    </blockquote>
                    <div class="p20">
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

                    </div> <!-- social links -->
                    <div class="clear"> &nbsp;</div>
                    <br>
                    <h3>Discovery </h3>
                    <ul>
                        <li>Find interesting items online or a at a location near you </li>
                        <li>Find products from multiple sources </li>
                        <li>Discover items not listed elsewhere as users share content </li>
                        <li>View likes and faves to get an idea of popularity of product </li>
                        <li>Ask questions to find out more about the product or Comment on what you feel about a product</li>
                    </ul>
                    <br>
                    <h3>Sharing </h3>
                    <ul>
                        <li> Network and share information which is not accessible </li>
                        <li> Promote your Brand, blog, product, site or store </li>
                        <li> Build reputation </li>
                        <li> Get to know people who look for your products or shares </li>
                    </ul>
                    

                    <br>
                    <h3> what is there? </h3>
                    <ul class="unordered">
                        <li> street food, best sandesh and jalebi!</li>
                        <li> Designer sarees, what is that vidya balan is wearing?</li>
                        <li> wood toys, warli art, puppets and handicrafts</li>
                        <li> Fashion accessories </li>

                        <li> Rajnikanth shot glasses, chota bheem bags and other cool stuff</li>
                        <li> chilli chocolate in Bangalore and Tappu Dabbawala in Mumbai</li>
                        <li> Home decor ideas and gift ideas</li>
                        <li> <a href="/">and lot more&nbsp;&rarr;</a></li>
                    
                    </ul>

                    
                </div> <!-- col:1 -->

                <div class="span6 offset1 ">
                    
                 
                    <div class="section1">
                    </div>

                    <div class="photo">
                        <img src="/site/images/collage/part-1.png" />
                    </div>
                    <div class="photo">
                        <img src="/site/images/collage/part-2.png" />
                    </div><!-- collage -->



                </div> <!-- col:2 -->
            </div> <!-- row -->
           

        </div>  <!-- container -->


        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>

        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.toolbar.add();
           });
        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
