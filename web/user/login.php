<?php
    include ('sc-app.inc');
    include ($_SERVER['APP_WEB_DIR'].'/inc/header.inc');
    
    use com\indigloo\Util;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    use com\indigloo\ui\form\Message as FormMessage;

	//do we already have a login?
	if(\com\indigloo\sc\auth\Login::isValid()) {
		header("Location: / ");
	}	
     
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

	$qUrl = "/" ;
	if(array_key_exists('q',$_GET) && !empty($_GET['q'])){
		$qUrl = $_GET['q'] ;
	}
    
    $stoken = Util::getMD5GUID();
    $gWeb->store("fb_state",$stoken);
   
    $fbAppId = Config::getInstance()->get_value("facebook.app.id");

	$host = "http://".$_SERVER["HTTP_HOST"];
    $fbCallback = $host."/callback/fb2.php" ;
    
    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email&state=".$stoken ;
    
?>  

<!DOCTYPE html>
<html>

       <head><title> 3mik.com - login page</title>
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/meta.inc'); ?>
         
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/css/sc.css">
		<script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
		
        <script type="text/javascript" src="/3p/jquery/jquery.validate.1.9.0.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){
                
                $("#web-form1").validate({
                    errorLabelContainer: $("#web-form1 div.error")
                });
                
            });
            
        </script>
					
							  
    </head>

     <body>
        
		<div class="container mh800">
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/toolbar.inc'); ?>
				</div> 
			</div>
			
			<div class="row">
				<div class="span12">
					<?php include($_SERVER['APP_WEB_DIR'] . '/inc/banner.inc'); ?>
				</div>
			</div>
			
			
			<div class="row">
				<div class="span12">
                    <div class="page-header">
                        <h2> Login Page </h2>
                    </div>
                    <div class="p20">
                        <?php FormMessage::render(); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="span7">
                  <div id="mik-login-wrapper">
                   <h3> You can login with 3mik.com account </h3>
                         No 3mik.com account? <a href="/user/register.php"> Register for a new account</a> (Free and takes just 30 seconds!)
                      
                        <form id="web-form1"  name="web-form1" action="/user/form/login.php" enctype="multipart/form-data"  method="POST">
                            <div class="error">    </div>

                            <table class="form-table">
                            <tr>
                                <td class="field"> Email<span class="red-label">*</span></td>
                                <td>
                                    <input type="text" name="email" maxlength="64" class="required" title="Email is required" value="<?php echo $sticky->get('email'); ?>"/>
                                </td>
                            </tr>

                             <tr>
                                <td class="field"> Password<span class="red-label">*</span></td>
                                <td>
                                    <input type="password" name="password" maxlength="32" class="required" title="Password is required" value=""/>
                                </td>
                            </tr>
                         
                        </table>

                        <div class="form-actions">
                            <button class="btn btn-primary" type="submit" name="login" value="Login" onclick="this.setAttribute('value','Login');" ><span>Login</span></button>
                            <a href="<?php echo $qUrl; ?>">
                                <button class="btn" type="button" name="cancel"><span>Cancel</span></button>
                            </a>
                            
                        </div>

                        <input type="hidden" name="q" value="<?php echo $qUrl; ?>" />
                        
                    </form>
                   </div> 
                    
                </div> <!-- span61 -->
                <div class="span5">
                    <div id="social-login-wrapper">
                     <div class="row">
                       <h3> - OR - </h3>
                       <div class="span5">
                           <div class="facebook-login">
                               <a href="<?php echo $fbDialogUrl; ?>"> Login with Facebook</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span5">
                            <div class="twitter-login">
                                <a href="/user/twitter-login.php">Login with Twitter</a> 
                            </div>
                        </div>
                    </div> <!-- row -->
                 </div> <!-- wrapper -->
                </div> <!-- span62 -->
            </div>

       </div> <!-- container -->
                        
        <?php include($_SERVER['APP_WEB_DIR'] . '/inc/site-footer.inc'); ?>

    </body>
</html>
