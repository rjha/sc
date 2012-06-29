<?php

    include("sc-app.inc");
    include(APP_CLASS_LOADER);

    use \com\indigloo\Util as Util  ;
    $parser = new \com\indigloo\text\UrlParser();
    $url ="http://mint.3mik.com";
    $response = $parser->extractUsingDom($url);
    $strResponse = json_encode($response);
    

?>

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
        </style>

        <script type="text/javascript">

            webgloo.sc.ImageSelector = {

                list : {},

                imageDiv : '<div id="image-{id}" class="stackImage" >' 
                    + '<div class="options"> <div class="links"> </div> </div>' 
                    + '<img src="{srcImage}" class="thumbnail-1" />  </div>' ,

                addLink : '<a id="{id}" class="btn btn-mini btn-inverse add-image" href="">Select</a>' ,
                removeLink : '<a id="{id}" class="btn btn-mini btn-danger remove-image" href="">Remove</a>' ,

                addImage : function(id,image) {
                    var buffer = this.imageDiv.supplant({"srcImage":image, "id":id } );
                    $("div#image-data").append(buffer);
                    this.list[id] = { "id":id, "link": image} ;
                },
                attachEvents : function() {

                    $('.stackImage .options').hide();

                    $('.stackImage').live("mouseenter",function() {
                        //will get image-1, image-2 etc.
                        var imageId = $(this).attr("id");
                        //will split into image and 1 
                        var ids = imageId.split('-'); 
                        var realId = ids[1] ;
                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        
                        //if this image is selected?
                        if(imageObj.selected) {
                            //show remove button in options area.
                            var buffer = webgloo.sc.ImageSelector.removeLink.supplant({"id":realId } );
                            $(this).find(".options .links").html(buffer);

                        } else {
                            //show Add+ button in options area.
                            var buffer = webgloo.sc.ImageSelector.addLink.supplant({"id": realId } );
                            $(this).find(".options .links").html(buffer);

                        }

                        $(this).find(".options").show();
                    });

                    $('.stackImage').live("mouseleave", function() {
                        $(this).find(".options").hide();
                    });

                    $('.add-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = true ;
                        webgloo.sc.ImageSelector.list.realId = imageObj ;

                        //change display
                        $(imageId).addClass("clicked-tile");
                        $(imageId).find('.options').hide();


                    });

                    $('.remove-image').live("click", function(event) {
                        event.preventDefault();
                        var realId = $(this).attr("id");
                        var imageId = "#image-" + realId ;

                        imageObj = webgloo.sc.ImageSelector.list[realId] ;
                        //change selected state for imageObj 
                        imageObj.selected = false ;
                        webgloo.sc.ImageSelector.list.realId = imageObj ;

                        //change display
                        $(imageId).removeClass("clicked-tile");
                        $(imageId).find('.options').hide();


                     });

                },
                
                load: function(images) {
                    for(i = 0 ; i < images.length ; i++) {
                        this.addImage(i,images[i]);
                    }
                }
    
            }


            $(document).ready(function(){

                var strResponseObj = '<?php echo $strResponse; ?>' ;

                try{
                    responseObj = JSON.parse(strResponseObj) ;
                    console.log(responseObj.title);
                    console.log(responseObj.description);
                    $("#title").html(responseObj.title);
                    $("#description").html(responseObj.description);
                    //image is an array
                    var images = responseObj.images ;
                    console.log(images);
                    webgloo.sc.ImageSelector.attachEvents();
                    webgloo.sc.ImageSelector.load(images);

                } catch(ex) {
                    console.log("Error parsing response object json");
                    console.log(ex.message);
                }

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
                        
                        <div id="title"> </div>
                        <div id="description"> </div>
                        <div id="image-data"> </div>

                    </div>
                    <div class="span3"> 
                        Row 2
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>
