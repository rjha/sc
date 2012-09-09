<?php

    include('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> terms of service page on 3mik </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>

        <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
        <div class="container mh600">
            
            <div class="row">
                <div class="span12">
                    <div class="page-header"> <h2> 3mik terms of service </h2> </div>

                        <div class="section1">

                            Welcome to our website. If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern 3mik.com's relationship with you in relation to this website. If you disagree with any part of these terms and conditions, please do not use our website.

The term '3mik.com' or 'us' or 'we' refers to the owner of the website. The term 'you' refers to the user or viewer of our website.
                        </div>
                        <h3> The use of this website is subject to the following terms of service</h3>
                        <ul>
                            <li> Neither we nor any third parties provide any warranty or guarantee as to the accuracy, timeliness, performance, completeness or suitability of the information and materials found or offered on this website for any particular purpose. You acknowledge that such information and materials may contain inaccuracies or errors and we expressly exclude liability for any such inaccuracies or errors to the fullest extent permitted by law.
                            </li>
                            <li>
                                The content of the pages of this website is for your general information and use only. It is subject to change without notice.
                            </li>
                            <li>
                                Your use of any information or materials on this website is entirely at your own risk, for which we shall not be liable. It shall be your own responsibility to ensure that any products, services or information available through this website meet your specific requirements.
                            </li>
                            <li>
                                We remain the right to delete any content that we deem spam or offensive. We are not liable to issue any notice before deleting spam or offensive content. Please do not try to spam and please do not try to hurt the sentiments of other users. You should respect the privacy and intellectual copyright of others. 
                            </li>
                            <li> We respect all copyrights and expect you to do the same. If there is a dispute because of copyright claims then we may ask you to take down your content. We expect all our users to comply with existing laws.

                        </ul>

                            

                </div>
            </div> <!-- row -->
            <hr>

        </div> <!-- container -->

        <?php echo \com\indigloo\sc\util\Asset::version("/js/bundle.js"); ?>
        <script type="text/javascript">
            $(function(){
                webgloo.sc.toolbar.add();
            });
        </script>

        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>



    </body>
</html>
