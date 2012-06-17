<?php

    //sc/site/contact.php
    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
        
    use com\indigloo\Util;
    use com\indigloo\Url;
    use \com\indigloo\sc\auth\Login as Login ;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;
    
    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;
    $fUrl = Url::current();

    //add security token to form
    $formToken = Util::getBase36GUID();
    $gWeb->store("form.token",$formToken);
    
?>  

<!DOCTYPE html>
<html>

       <head>
        <title> 3mik.com - contact us </title>
        <?php include(APP_WEB_DIR . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>

        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?> 
         
        <script type="text/javascript">
       
            $(document).ready(function(){
               
                $("#web-form1").validate({
                       errorLabelContainer: $("#web-form1 div.error") 
                });

                webgloo.sc.util.addTextCounter("#comment", "#comment_counter");

            });
            
        </script>
       
       
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div> 
                
            </div>
            
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/banner.inc'); ?>
                </div>
            </div>
            
            
            <div class="row">
                <div class="span8">
                    
                    
                    <div class="page-header">
                        <h2> Contact Us </h2>
                    </div>
                    
                    <?php FormMessage::render(); ?>
                    
                    <form  id="web-form1"  name="web-form1" action="/site/form/contact.php" enctype="multipart/form-data"  method="POST">
                        <table class="form-table">
                             <tr>
                                <td> <label>Name*</label>
                                <input type="text" name="name" class="required" maxlength="64" value="<?php echo $sticky->get('name'); ?>" />
                                </td>
                            </tr>
                             <tr>
                                <td> <label>Email* </label>
                                <input type="text" name="email" class="required" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                                </td>
                            </tr>
                             <tr>
                                <td> <label>phone</label>
                                <input type="text" name="phone" maxlength="32" value="<?php echo $sticky->get('phone'); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Comments* (max 512 chars)</label>
                                    <textarea  id="comment" name="comment" maxlength="512" class="required h130 w500" cols="50" rows="4" ><?php echo $sticky->get('comment'); ?></textarea>
                                    <br>
                                   <span id="comment_counter"></span> 
                                </td>
                            </tr>
                            
                        </table>
                        
                        
                    
                        <div class="form-actions"> 
                            <button class="btn btn-primary" type="submit" name="save" value="Save" onclick="this.setAttribute('value','Save');" ><span>Submit</span></button> 
                            <a href="<?php echo $qUrl; ?>"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                        </div>
                        <input type="hidden" name="token" value="<?php echo $formToken; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                                
                    </form>
                    
                                    
                   
                </div> <!-- content -->
                
                <div class="span4">
                    <!-- sidebar -->
                    <div class="noresults">
                        our email is <br>
                        support@3mik.com
                    </div>
                </div>
            
            </div>
            
        </div> <!-- container -->   
                      
        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>
