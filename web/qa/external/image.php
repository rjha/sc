<?php

    include ('sc-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Message as FormMessage;

    $fUrl = Url::current();
    $qUrl = Url::tryQueryParam("q");
    $qUrl = is_null($qUrl) ? '/' : $qUrl ;


?>

<!DOCTYPE html>
<html>

    <head>
        <title> share a  webpage </title>

        <meta charset="utf-8">

        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">
        <?php echo \com\indigloo\sc\util\Asset::version("/css/sc.css"); ?> 

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/3p/bootstrap/js/bootstrap.js"></script>
        <?php echo \com\indigloo\sc\util\Asset::version("/js/sc.js"); ?> 

        <style>
            #step1-container { margin-top:10px; padding:10px; }
            #step2-container { margin-top:10px; padding:10px; border-left:1px solid #f7f7f7;}
            #link-box {width:280px; }
            #fetch-button {width:60px; height:28px; margin-bottom:10px;}
            #next-button {width:60px; height:28px; margin-bottom:10px;}

            #stack { margin-top:40px; }
            /* override default stack image padding */
            div .stackImage { padding: 1px; }
        </style>

        <script type="text/javascript">


            webgloo.sc.ImageSelector = {

                bucket : {},
                images : [],
                num_total: 0 ,
                num_added : 0 ,
                num_processed : 0 ,
                num_selected : 0 ,
                num_uploaded : 0 ,
                debug : false ,

                extractEndpoint : "/qa/ajax/extract-image.php",
                uploadEndpoint : "/upload/image.php" ,
                nextEndpoint : "/qa/external/router.php" ,

                imageDiv : '<div id="image-{id}" class="stackImage" >' 
                    + '<div class="options"> <div class="links"> </div> </div>' 
                    + '<img src="{srcImage}" class="thumbnail-1" /> </div>' ,

                addLink : '<a id="{id}" class="btn btn-mini add-image" href="">Select</a>' ,
                removeLink : '<i class="icon-ok"></i>&nbsp;&nbsp;'  
                    + '<a id="{id}" class="btn btn-mini remove-image" href="">Remove</a>' ,

                init:function() {
                    //reset counters and buckets
                    webgloo.sc.ImageSelector.num_total = 0 ;
                    webgloo.sc.ImageSelector.num_added = 0 ;
                    webgloo.sc.ImageSelector.num_processed = 0 ;
                    webgloo.sc.ImageSelector.num_selected = 0 ;
                    webgloo.sc.ImageSelector.num_uploaded = 0 ;
                    webgloo.sc.ImageSelector.bucket = {} ;
                    webgloo.sc.ImageSelector.images = [] ;
                    webgloo.sc.ImageSelector.clearMessage();
                },

                attachEvents : function() {

                    $('.stackImage .options').hide();
                    $('#stack').hide();
                    //@debug
                    $('#step2-container').hide();

                    $("#fetch-button").live("click", function(event){
                        event.preventDefault();
                        var link = jQuery.trim($("#link-box").val());
                        if( link == '' ){
                            return ;
                        } else {
                            webgloo.sc.ImageSelector.fetch(link);
                        }
                    }) ;

                    //capture ENTER on link box
                    $("#link-box").keydown(function(event) {
                        //donot submit form
                        if(event.which == 13) {
                            event.preventDefault();
                            var link = jQuery.trim($("#link-box").val());
                            if( link == '' ) {
                                return ;
                            } else{
                                webgloo.sc.ImageSelector.fetch(link);
                            }
                        }

                    });
                    
                    $('#next-button').live("click",function() {

                        //initialize
                        webgloo.sc.ImageSelector.clearMessage();
                        webgloo.sc.ImageSelector.num_uploaded = 0  ;

                        var counter = 1 ;

                        if(webgloo.sc.ImageSelector.debug) {
                            console.log("num_selected :: " + webgloo.sc.ImageSelector.num_selected);
                        }

                        if(webgloo.sc.ImageSelector.num_selected == 0 ) {
                            webgloo.sc.ImageSelector.showMessage("Please select an image.",{"css":"color-red"});
                            return false;

                        } else {
                            $("#stack .images").find('.stackImage').each(function(index) {
                                var imageId = $(this).attr("id");
                                //will split into image and 1 
                                var ids = imageId.split('-'); 
                                var realId = ids[1] ;
                                var imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
                                

                                if(imageObj.selected) {
                                    webgloo.sc.ImageSelector.upload(counter,imageObj.srcImage);
                                    if(webgloo.sc.ImageSelector.debug) {
                                        console.log("upload image :: " + imageObj.srcImage);
                                    }
                                    counter++ ;
                                }

                            });
                        }

                    });

                    $('.stackImage').live("mouseenter",function() {
                        //will get image-1, image-2 etc.
                        var imageId = $(this).attr("id");
                        //will split into image and 1 
                        var ids = imageId.split('-'); 
                        var realId = ids[1] ;
                        imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
                        
                        if(!imageObj.selected) {
                            // show select button 
                            var buffer = webgloo.sc.ImageSelector.addLink.supplant({"id": realId } );
                            $(this).find(".options .links").html(buffer);
                        } 

                        $(this).find(".options").show();
                    });

                    $('.stackImage').live("mouseleave", function() {
                        //will get image-1, image-2 etc.
                        var imageId = $(this).attr("id");
                        //will split into image and 1 
                        var ids = imageId.split('-'); 
                        var realId = ids[1] ;
                        imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
                        
                        //if this image is selected?
                        if(!imageObj.selected) {
                            $(this).find(".options").hide();
                        }

                    });

                    $('.add-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = true ;
                        webgloo.sc.ImageSelector.bucket.realId = imageObj ;
                        webgloo.sc.ImageSelector.num_selected++ ;

                        // change display
                        var buffer = webgloo.sc.ImageSelector.removeLink.supplant({"id":realId } );
                        $(imageId).find(".options .links").html(buffer);

                    });

                    $('.remove-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.bucket[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = false ;
                        webgloo.sc.ImageSelector.bucket.realId = imageObj ;
                        webgloo.sc.ImageSelector.num_selected-- ;
                        $(imageId).find('.options').hide();
                     });

                },
                
             

                addSpinner : function() {
                    $("#block-spinner").html('');
                    var content = '<div> Please wait...</div> ' 
                        + '<div> <img src="/css/images/6RMhx.gif" alt="loading ..." /> </div>' ;
                    $("#block-spinner").html(content);

                    /* show mask */
                    var maskHeight = $(document).height();
                    var maskWidth = $(window).width();
                    $("#popup-mask").css({'width':maskWidth,'height':maskHeight});
                    $("#popup-mask").show();

                    /* show spinner */
                    $("#block-spinner").show();

                },

                removeSpinner: function() {
                    $("#block-spinner").html('');
                    $("#popup-mask").hide();
                    $("#block-spinner").hide();
                },

                appendMessage : function(message,options) {
                    options.css = (typeof options.css === "undefined") ? '' : options.css;
                    $("#ajax-message").append("<div> " + message + "</div>");
                    if( options.css != '') {
                        $("#ajax-message").addClass(options.css);
                    }

                },

                clearMessage : function() {
                    $("#ajax-message").html('');
                },

                showMessage : function(message,options) {
                    options.css = (typeof options.css === "undefined") ? '' : options.css;

                    $("#ajax-message").html('');
                    $("#ajax-message").html("<div> " + message + "</div>");

                    if( options.css != '') {
                        $("#ajax-message").addClass(options.css);
                    }

                },

				loadImage : function(img) {
					if((img.width > 400) && (img.height > 200 )) {
						webgloo.sc.ImageSelector.addImage(img.src);
					}

				},

				makeLoadImage : function(img) {
					return function() {
						webgloo.sc.ImageSelector.loadImage(img);
					};
				},

                showNextButton : function() {

                    if(webgloo.sc.ImageSelector.debug) {
                        console.log("show_next_button with num_added= " + webgloo.sc.ImageSelector.num_added); 
                    }

                    if(webgloo.sc.ImageSelector.num_added > 0 ) {
                        $("#step2-container").fadeIn("slow");
                    }else {
                        var message = "Error: No suitable images found";
                        webgloo.sc.ImageSelector.showMessage(message, {"css":"color-red"});
                    }

                },

                addImage : function(image) {

                    var index = webgloo.sc.ImageSelector.num_added ;

                    if(webgloo.sc.ImageSelector.debug) {
                        console.log("Adding image : " + index + " : " + image);
                    }

                    var buffer = this.imageDiv.supplant({"srcImage":image, "id":index } );
                    //logo, small icons etc. are first images in a page
                    // what we are interested in will only come later.
                    $("div#stack .images").prepend(buffer);

                    this.bucket[index] = { "id":index, "srcImage": image, "selected" : false} ;
                    webgloo.sc.ImageSelector.num_added++ ;
                },

                processUrlFetch : function(response) {
                    images = response.images ;
                    webgloo.sc.ImageSelector.num_total = images.length;

                    for(i = 0 ; i < images.length ; i++) {
						var img = new Image();
                        
                        // @warning closure inside a loop
                        // do not use outer function variables.
                        img.onload = function() {
                            webgloo.sc.ImageSelector.num_processed++ ;
                            if(this.width == 0 || this.height == 0 ) {
                                thisonerror();
                            }

                            if((this.width > 100) && (this.height > 100 )) {
                                webgloo.sc.ImageSelector.addImage(this.src);
                            }

                            if(webgloo.sc.ImageSelector.num_processed == webgloo.sc.ImageSelector.num_total) {
                                webgloo.sc.ImageSelector.showNextButton();
                            }
                        }

                        img.onerror = function() {
                            webgloo.sc.ImageSelector.num_processed++ ;
                            if(webgloo.sc.ImageSelector.num_processed == webgloo.sc.ImageSelector.num_total) {
                                webgloo.sc.ImageSelector.showNextButton();
                            }
                        }

                        img.onabort = function() {
                            webgloo.sc.ImageSelector.num_processed++ ;
                            if(webgloo.sc.ImageSelector.num_processed == webgloo.sc.ImageSelector.num_total) {
                                webgloo.sc.ImageSelector.showNextButton();
                            }
                        }

                        img.src = images[i] ;
                    }

                    $("#stack").fadeIn("slow");

                },

                fetch : function(target) {

                    webgloo.sc.ImageSelector.init();
                    webgloo.sc.ImageSelector.addSpinner();

                    $("#stack").fadeOut("slow");
                    $("#stack .images").html('');

                    endPoint = webgloo.sc.ImageSelector.extractEndpoint ;
                    params = {} ;
                    params.target = target ;
                    //ajax call start
                    $.ajax({
                        url: endPoint,
                        type: "POST",
                        dataType: "json",
                        data :  params,
                        timeout: 9000,
                        processData:true,
                        //js errors callback
                        error: function(XMLHttpRequest, response){
                            webgloo.sc.ImageSelector.removeSpinner();
                            webgloo.sc.ImageSelector.showMessage(response, {"css":"color-red"});
                        },

                        // server script errors are also reported inside 
                        // ajax success callback
                        success: function(response){
                            if(webgloo.sc.ImageSelector.debug) {
                                console.log("server response for image fetch :: ") ;
                                console.log(response);
                            }

                            webgloo.sc.ImageSelector.removeSpinner();

                            switch(response.code) {
                                case 401 :
                                    webgloo.sc.ImageSelector.showMessage(response.message,{"css":"color-red"});
                                    break ;
                                case 200 :
                                    webgloo.sc.ImageSelector.processUrlFetch(response);
                                    break ;
                                default:
                                    webgloo.sc.ImageSelector.showMessage(response.message,{"css":"color-red"});
                                    break ;
                            }
                        }

                    }); //ajax call end


                },

                processImageUpload : function(counter,response) {
                    mediaVO = response.mediaVO ;
                    webgloo.sc.ImageSelector.images.push(mediaVO);
                    webgloo.sc.ImageSelector.num_uploaded++ ;

                    if(counter == webgloo.sc.ImageSelector.num_selected) {
                        //Actual upload?
                        if(webgloo.sc.ImageSelector.num_uploaded > 0 ) {
                            //stringify images
                            var strImagesJson =  JSON.stringify(webgloo.sc.ImageSelector.images);
                            //bind to form
                            frm = document.forms["web-form1"];
                            frm.images_json.value = strImagesJson ;
                            $('#web-form1').submit();
                        }

                    }
                },

                upload : function(counter,imageUrl) {
                    webgloo.sc.ImageSelector.images = new Array();

                    var message = " uploading image {upload}/{total} ... " ;
                    message = message.supplant({"upload":counter, "total":webgloo.sc.ImageSelector.num_selected});
                    webgloo.sc.ImageSelector.appendMessage(message,{});
                    //$("#stack .images").html('');

                    var options = {"css":"color-red"} ;
                    var prefix = "image " + counter + " : " ;

                    endPoint = webgloo.sc.ImageSelector.uploadEndpoint ;
                    params = {} ;
                    params.qqUrl = imageUrl ;
                    //ajax call start
                    $.ajax({
                        url: endPoint,
                        type: "POST",
                        dataType: "json",
                        data :  params,
                        timeout: 9000,
                        processData:true,
                        //js errors callback
                        error: function(XMLHttpRequest, response){
                            webgloo.sc.ImageSelector.appendMessage(response,options);
                        },

                        // server script errors are also reported inside 
                        // ajax success callback
                        success: function(response){
                            if(webgloo.sc.ImageSelector.debug) {
                                console.log("upload response for image :: " + imageUrl);
                                console.log(response);
                            }

                            switch(response.code) {
                                case 401 :
                                    webgloo.sc.ImageSelector.appendMessage(prefix + response.message,options);
                                    break ;
                                case 200 :
                                    webgloo.sc.ImageSelector.processImageUpload(counter,response);
                                    break ;
                                default:
                                    webgloo.sc.ImageSelector.appendMessage(prefix + response.message,options);
                                    break ;
                            }
                        }

                    }); //ajax call end

                }  
    
            }

            $(document).ready(function(){
                webgloo.sc.ImageSelector.debug= true ;
                webgloo.sc.ImageSelector.attachEvents();

            });

        </script>

    </head>

    <body>
        <div class="container mh600">
            <div class="row">
                <div class="span12">
                    <?php include(APP_WEB_DIR . '/inc/toolbar.inc'); ?>
                </div> 
            </div>
            
            <div class="hr"> </div>
            <?php FormMessage::render(); ?>

            <div class="row">
                <div class="span6">
                    <div id="step1-container">
                        <span class="badge badge-warning">Step1</span>
                        Type webpage URL and click fetch ( or press Enter ) 
                        <br>
                        <br>
                        <input id="link-box" name="link" value="" />
                        <button id="fetch-button" type="button" class="btn" value="Fetch">Fetch</button> 
                    </div>
                    
                </div> <!-- span -->
                <div class="span6">
                    <div id="step2-container">
                        <p>
                            <span class="badge badge-warning">Step2</span>
                            Place your mouse over an image to select it. 
                            Please click Next button after selecting images.
                        </p>
                        <div style="margin-left:120px;">
                            <button id="next-button" type="button" class="btn" value="next">Next&nbsp;</button> 
                        </div>
                        <form  id="web-form1"  name="web-form1" action="/qa/external/router.php"  method="POST">
                            <input type="hidden" name="images_json" />
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div> <!-- step2-container -->

                </div>

            </div><!-- row:1 -->
            
            <div class="hr"> </div>
            <div id="ajax-message" class="ml20"> </div>

            <div class="row">
                <div id="stack"> 
                    <div class="images p10"> </div>
                </div>
            </div> <!-- row:2 -->
           
        </div> <!-- container -->   
              
        <div id="ft">
            <?php include(APP_WEB_DIR . '/inc/site-footer.inc'); ?>
        </div>

    </body>
</html>

