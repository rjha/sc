<!DOCTYPE html>
<html>

    <head>
        <title> Image extractor test page</title>

        <meta charset="utf-8">

        <link rel="stylesheet" type="text/css" href="/css/sc.css">
        <link rel="stylesheet" type="text/css" href="/3p/bootstrap/css/bootstrap.css">

        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/js/sc.js"></script>
        <style>
            /* override default stack image padding */
            div .stackImage { padding: 1px; }
            .form-table { margin-bottom : 5px; }
        </style>

        <script type="text/javascript">

            webgloo.sc.ImageSelector = {

                list : {},

                imageDiv : '<div id="image-{id}" class="stackImage" >' 
                    + '<div class="options"> <div class="links"> </div> </div>' 
                    + '<img src="{srcImage}" class="thumbnail-1" /> </div>' ,

                addLink : '<a id="{id}" class="btn btn-inverse btn-mini add-image" href="">Select</a>' ,
                removeLink : '<i class="icon-ok"></i>&nbsp;&nbsp;'  
                    + '<a id="{id}" class="btn btn-inverse btn-mini remove-image" href="">Remove</a>' ,

                attachEvents : function() {

                    $('.stackImage .options').hide();
                    $('#stack').hide();
                    $('#next-message').hide();

                    $("#fetch-link").live("click", function(event){
                        event.preventDefault();
                        var link = jQuery.trim($("#link-box").val());
                        if( link == '' )
                            return ;
                        else
                            webgloo.sc.ImageSelector.fetch(link);
                    }) ;

                    //capture ENTER on link box
                    $("#link-box").keydown(function(event) {
                        //donot submit form
                        if(event.which == 13) {
                        event.preventDefault();
                        var link = jQuery.trim($("#link-box").val());
                        if( link == '' )
                            return ;
                        else
                            webgloo.sc.ImageSelector.fetch(link);
                        }

                    });
                    
                    $('#web-form1').submit(function() {
                        var count = webgloo.sc.ImageSelector.populateHidden();
                        if(count > 0 ) {
                            return true ;
                        } else {
                            webgloo.sc.ImageSelector.showMessage("No image selected",{"css":"color-red"});
                            return false;
                        }
                    });

                    $('.stackImage').live("mouseenter",function() {
                        //will get image-1, image-2 etc.
                        var imageId = $(this).attr("id");
                        //will split into image and 1 
                        var ids = imageId.split('-'); 
                        var realId = ids[1] ;
                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        
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
                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        
                        //if this image is selected?
                        if(!imageObj.selected) {
                            $(this).find(".options").hide();
                        }

                    });

                    $('.add-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = true ;
                        webgloo.sc.ImageSelector.list.realId = imageObj ;

                        // change display
                        var buffer = webgloo.sc.ImageSelector.removeLink.supplant({"id":realId } );
                        $(imageId).find(".options .links").html(buffer);

                    });

                    $('.remove-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = false ;
                        webgloo.sc.ImageSelector.list.realId = imageObj ;
                        $(imageId).find('.options').hide();
                     });

                },
                
                addImage : function(id,image) {
                    var buffer = this.imageDiv.supplant({"srcImage":image, "id":id } );
                    //logo, small icons etc. are first images in a page
                    // what we are interested in will only come later.
                    $("div#stack .images").prepend(buffer);
                    this.list[id] = { "id":id, "link": image, "selected" : false} ;
                },

                addSpinner : function() {
                    var content = '<img src="/css/images/ajax_loader.gif" alt="loading ..." />' ;
                    this.showMessage(content,{});
                },

                removeSpinner: function() {
                    this.showMessage('',{});
                },

                showMessage : function(message,options) {
                    options.css = (typeof options.css === "undefined") ? '' : options.css;
                    $("#ajax-message").html('');
                    $("#ajax-message").html(message);
                    if( options.css != '') {
                        $("#ajax-message").addClass(options.css);
                    }

                },

                processResponse : function(response) {

                    images = response.images ;
                    for(i = 0 ; i < images.length ; i++) {
                        this.addImage(i,images[i]);
                    }

                    $("#stack").fadeIn("slow");
                    $("#next-message").fadeIn("slow");

                },

                populateHidden : function() {
                    frm = document.forms["web-form1"];
                    //@debug
                    console.log(webgloo.sc.ImageSelector.list);
                    var links = new Array() ;

                    $("#stack .images").find('.stackImage').each(function(index) {
                        var imageId = $(this).attr("id");
                        //will split into image and 1 
                        var ids = imageId.split('-'); 
                        var realId = ids[1] ;
                        var imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        //
                        //@debug
                        console.log("Examining" + imageObj.link);

                        if(imageObj.selected) {
                            links.push(imageObj.link) ;
                            //@debug
                            console.log("Added" + imageObj.link);
                        }


                    });

                    var strLinks = JSON.stringify(links);
                    frm.images_json.value = strLinks ;
                    return links.length ;
                },

                fetch : function(target) {
                    webgloo.sc.ImageSelector.addSpinner();
                    $("#stack").fadeOut("slow");
                    $("#stack .images").html('');

                    endPoint = "/qa/ajax/extract-image.php" ;
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
                            //@debug
                            //console.log(response);
                            webgloo.sc.ImageSelector.removeSpinner();
                            webgloo.sc.ImageSelector.showMessage(response, {"css":"color-red"});
                        },

                        // server script errors are also reported inside 
                        // ajax success callback
                        success: function(response){
                            //@debug
                            //console.log(response);
                            webgloo.sc.ImageSelector.removeSpinner();
                            switch(response.code) {
                                case 401 :
                                    webgloo.sc.ImageSelector.showMessage(response.message,{"css":"color-red"});
                                    break ;
                                case 200 :
                                    webgloo.sc.ImageSelector.processResponse(response);
                                    break ;
                                default:
                                    webgloo.sc.ImageSelector.showMessage(response.message,{"css":"color-red"});
                                    break ;
                            }
                        }

                    }); //ajax call end


                } 
    
            }


            $(document).ready(function(){
                webgloo.sc.ImageSelector.attachEvents();

            });
        </script>

    </head>

     <body>
        <div class="row">
            <div class="span12">
                <div class="page-header">
                    <h2> Image extractor test page </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="span12">
                <div class="row">
                    <div class="span9"> 
                        <div class="row">
                            <div class="span6">
                                <table class="form-table">
                                <tr>
                                <td>
                                    <label>Type URL and click fetch ( or press Enter ) </label>
                                    <input id="link-box" name="link" value="" />
                                    <br/>
                                    <button id="fetch-link" type="button" class="btn" value="Fetch">Fetch</button> 
                                </td>
                                </tr>
                                </table>
                                <div id="ajax-message" class="ml20 p10"> </div>
                            </div> <!-- 1:span6 -->

                            <div class="span3">
                                <div id="next-message" class="p20 alert">
                                    <p> Please select the images below and click on Next button. </p>
                                    <form  id="web-form1"  name="web-form1" action="/qa/external/form/next.php"  method="POST">
                                        <button id="next-button" class="btn" type="submit" name="next" value="Next" onclick="this.setAttribute('value','Next');" ><span>Next&nbsp;&raquo;</span></button> 

                                        <input type="hidden" name="images_json" />
                                    </form>
                                </div>
                            </div> <!-- 1:span3 -->
                        </div> <!-- row:1 -->
                        <div class="row">
                            <div class="hr"> </div>
                            <div id="stack"> 
                                <div class="images p10"> </div>
                            </div> <!-- stack -->
                        </div> <!-- row:2 -->
                    </div> <!-- span9 -->
                    <div class="span3"> 
                        Row 2
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>
