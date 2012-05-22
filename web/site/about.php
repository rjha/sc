<?php
    include 'sc-app.inc';
    include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - Learn more  </title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="/3p/jquery/masonary/jquery.masonry.min.js"></script>

        <script type="text/javascript" src="/js/sc.js"></script>
        
        
        <script type="text/javascript">
            $(document).ready(function(){
                webgloo.sc.home.addTiles();
                webgloo.sc.home.addNavGroups();
           });
        </script>
        
    </head>

     <body class="">
        <div class="container mh800">
            <div class="row">
                <div class="span12">
                    <?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
                </div> 
                
            </div>
            
            <div class="row">
                <div class="span12">
                    <?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
                    <?php include($_SERVER['APP_WEB_DIR'] . '/inc/browser.inc'); ?>
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
                            <img src="/css/images/about-collage.jpg" />

                        </div>


                    </div>

                </div>
            </div>
            <div class="row p20" style="border-top:1px solid #DEDEDE;">
                <div class="span6 offset4">
                    <a class="btn" href="/user/register.php">Register</a>
                    &nbsp;
                    <small>Free and takes only a min.</small>
                </div>
            </div>
            <div class="row mt20">
                <div class="span4 offset2">
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
                        <li> <a href="/">and lot more&rarr;</a></li>
                    </ul>
                </div>

            </div>
                    
        </div>  <!-- container -->
              
       
        <div id="ft">
            <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
