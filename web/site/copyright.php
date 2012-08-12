<?php

    include('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> copyright page on 3mik </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/bundle.css"); ?>

    </head>

    <body>
        <div class="container mh600">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/slim-toolbar.inc'); ?>
                </div>
            </div>
            <div class="row">
                <div class="span12">
                    <div class="page-header"> <h2> Copyright </h2> </div>

                        <div class="paragraph">
                            3mik.com respects the intellectual property rights of others and expects its users to do
                            the same. 3mik.com in appropriate circumstances and at its discretion reserves rights to
                            disable and/or terminate the accounts of users who repeatedly infringe or are repeatedly
                            charged with infringing the copyrights or other intellectual property rights of others.
                            <br>
                            We follow Digital Millennium Copyright Act (DMCA) of 1998, the text can be found at
                            <a href="http://www.copyright.gov/legislation/dmca.pdf">DMCA text</a>
                            3mik.com will respond expeditiously to claims of copyright infringement committed using
                            the 3mik.com website that are reported to us.
                        </div>
                        <div class="paragraph">

                        If you are a copyright owner, or are authorized to act on behalf of one, or authorized to act
                        under any exclusive right under copyright, please report alleged copyright infringements taking
                        place by mailing the details to support@3mik.com. Upon receipt of the Notice as described below,
                        3mik.com will take whatever action, in its sole discretion, it deems appropriate, including
                        removal of the challenged material from the Site.
                        </div>
                        <div class="paragraph">
                        <h3> The mail should clearly mention: </h3>

                        a.)Identify the copyrighted work that you claim has been infringed, or - if multiple
                        copyrighted works are covered by this Notice - you may provide a representative list of the
                        copyrighted works that you claim have been infringed.
                        <br>
                        b.) Identify
                        <br>(i) the material that you claim is infringing and that is to be removed or access to which
                            is to be disabled by mentioning the URL of the link shown on 3mik.com, and

                        <br> (ii) the reference or source link, to the material or activity that you claim to be
                        infringing, that is to be removed or access to which is to be disabled, and information
                        reasonably sufficient to permit us to locate that reference or link, including at a minimum,
                        if applicable, the URL of the link shown on the Site where such reference or link may be found.

                        <br> c.) Provide your mailing address, telephone number, and, email address.
                        <div class="paragraph">
                        <h3> What if I receive a Copyright Complaint notification? </h3>

                            If you receive a notification that a post has been removed due a copyright complaint,
                            it means that the post’s content has been deleted from 3mik.com at the request of the
                            content’s owner. If your account receives too many copyright complaints, your account may
                            be disabled completely.

                            If you believe a post was removed in error, you have the option to file a counter-notice by
                            following the steps below. When we receive a valid counter-notice, we will forward a copy to
                            the person who filed the original complaint. If we do not receive mail within 10 business
                            days that the submitter of the original complaint is seeking a court order to prevent further
                            infringement of the content at issue, we will remove the complaint from your account’s
                            record, and we may replace the content that was removed.
                        </div>
                        <div class="paragraph">
                        <h3> To File a Counter-Notice send a mail to support@3mik.com with the following details: </h3>

                        1. Reply to the notification email you received.
                        <br> 2. Include ALL of the following:
                        <br> Your name, address, and telephone number.
                        <br> The source address of the content that was removed
                             (copy and paste the link in the notification email).
                        <br> A statement that you have a good faith belief that the content was removed in error.

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
