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

     <body class="">
        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div>

            </div>

            <div class="row">
                <div class="span4 offset1">
                    <h1> 3mik is a sharing and discovery platform in India.</h1>
                    <blockquote class="pull-right">
                        <p>
                    3mik lets you share interesting and unique things in India. You can use 3mik to discover your interests and see stuff shared by others. Move around and see what you like!
                        </p>
                    </blockquote>
                </div>
                <div class="span6 offset1 ">
                <!-- collage -->
                    <div>
                        <div class="photo">
                            <img src="/css/asset/sc/about-collage.jpg" />

                        </div>


                    </div>

                </div>
            </div> <!-- row -->
            <div class="row">
                <div class="span5 p20">
                    <h3>Discovery </h3>
                    <ul>
                        <li>Find interesting items online or a at a location near you </li>
                        <li>Find products from multiple sources </li>
                        <li>Discover items not listed elsewhere as users share content </li>
                        <li>View likes and faves to get an idea of popularity of product </li>
                        <li>Ask questions to find out more about the product or Comment on what you feel about a product</li>
                    </ul>
                </div>
                <div class="span5 p20">
                    <h3>Sharing </h3>
                    <ul>
                        <li> Network and share information which is not accessible </li>
                        <li> Promote your Brand, blog, product, site or store </li>
                        <li> Build reputation </li>
                        <li> Get to know people who look for your products or shares </li>
                    </ul>

                </div>
            </div> <!-- row -->

            <div class="row p20" style="border-top:1px solid #DEDEDE;">
                <div class="span6 offset4">
                    <a class="btn btn-primary" href="/user/register.php">Register a 3mik account</a>
                    &nbsp;
                    <small>Free and takes only a min.</small>
                </div>
            </div>
            <div class="row mt20">
                <div class="span6 offset2">
                    <h2> what is there? </h2>
                    <ul class="unordered">
                        <li> street food, best sandesh and jalebi!</li>
                        <li> Designer sarees, what is that vidya balan is wearing?</li>
                        <li> wood toys, warli art, puppets and handicrafts</li>
                        <li> Fashion accessories </li>

                    </ul>
                </div>
                <div class="span4">
                    <ul class="unordered">
                        <li> Rajnikanth shot glasses, chota bheem bags and other cool stuff</li>
                        <li> chilli chocolate in Bangalore and Tappu Dabbawala in Mumbai</li>
                        <li> Home decor ideas and gift ideas</li>
                        <li> <a href="/">and lot more&nbsp;&rarr;</a></li>
                    </ul>
                </div>

            </div>

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
