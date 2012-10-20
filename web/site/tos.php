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

                            Welcome to our website. If you continue to browse and use this website, 
                            you are agreeing to comply with and be bound by the following terms and 
                            conditions of use, which together with our privacy policy govern 3mik.com's 
                            relationship with you in relation to this website. If you disagree with any 
                            part of these terms and conditions, please do not use our website or sevrices.
                            The term '3mik.com' or 'us' or 'we' refers to the owner of the website. 
                            The term 'you' refers to the user or viewer of our website.
                            3mik reserves the right to change these Terms at any time. 
                            We recommend that you periodically check this Site for changes.

                        </div>
                        <div class="section">
                            <h3> Prohibited Uses </h3>
                            You may not use the 3mik site and or its services to transmit 
                            any content which:
                            <ul>
                                <li> harasses, threatens, embarrasses or causes distress, 
                                    unwanted attention or discomfort upon any other person,
                                </li>
                                <li>
                                    includes sexually explicit images or other content which is offensive 
                                    or harmful to minors,
                                </li>
                                <li> includes any unlawful, harmful, threatening, abusive, harassing, 
                                    defamatory, vulgar, obscene, or hateful material, including 
                                    but not limited to material based on a person's race, national origin, 
                                    ethnicity, religion, gender, sexual orientation, disablement or 
                                    other such affiliation,</li>
                                
                                <li>impersonates any person or the appearance or voice of any person,</li>
                                <li> utilizes a false name or identity or a name or identity that you are 
                                    not entitled or authorized to use, </li>
                                <li>contains any unsolicited advertising, promotional materials, or 
                                    other forms of solicitation,</li>
                                <li>contravenes any application law or government regulation,</li>
                                <li> violates any operating rule, regulation, procedure, policy or 
                                    guideline of 3mik as published on the 3mik website,</li>
                                <li> may infringe the intellectual property rights or other rights of 
                                    third parties, including trademark, copyright, trade secret, patent, 
                                    publicity right, or privacy right,distributes software or other 
                                    Content in violation of any license agreement.
                                </li>
                           
                            </ul>
                        </div>
                        <div class="section">
                            <h3> No Warranty and Limitation of Liability</h3>
                            <ul>
                                <li> Neither we nor any third parties provide any warranty or 
                                    guarantee as to the accuracy, timeliness, performance, 
                                    completeness or suitability of the information and materials 
                                    found or offered on this website for any particular purpose. 
                                    You acknowledge that such information and materials may contain 
                                    inaccuracies or errors and we expressly exclude liability for any 
                                    such inaccuracies or errors to the fullest extent permitted by law.
                                </li>
                                <li>
                                    The content of the pages of this website is for your general 
                                    information and use only. It is subject to change without notice.
                                </li>
                                <li>
                                    Your use of any information or materials on this website is 
                                    entirely at your own risk, for which we shall not be liable. 
                                    It shall be your own responsibility to ensure that any products, 
                                    services or information available through this website meet your specific requirements.
                                </li>
                            </ul>
                        </div>
                        <div class="section">
                            <h3> Other</h3>
                            <ul>
                                <li>
                                    We remain the right to delete any content that we deem spam or offensive. We are not liable to issue any notice before deleting spam or offensive content. Please do not try to spam and please do not try to hurt the sentiments of other users. You should respect the privacy and intellectual copyright of others. 
                                </li>
                               

                            </ul>
                        </div>
                            

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
