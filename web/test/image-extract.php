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
            .yellow { background: yellow ; }
            .whiteSmoke { background: whiteSmoke ; }
        </style>

        <script type="text/javascript">

            webgloo.sc.ImageSelector = {

                buffer : [],

                imagePreviewDiv : '<div id="image-{id}" class="stackImage" >' +
                    '<div class="options"> <a id="{id}" class="btn btn-mini select-image" href="">Add +</a></div> ' +
                    ' <img src="{srcImage}" class="thumbnail-1" />  </div>' ,

                imageSelectedDiv : '<div> <i class="icon-ok"> </i> </div>' ,

                addImage : function(id,image) {
                    var buffer = this.imagePreviewDiv.supplant({"srcImage":image, "id":id } );
                    $("div#image-data").append(buffer);
                },
                attachEvents : function() {
                    $('.stackImage .options').hide();
                    $('.stackImage').live("mouseenter",function() {
                        $(this).find('.options').toggle();
                    });
                    $('.stackImage').live("mouseleave", function() {
                        $(this).find('.options').toggle();
                    });

                    $('.select-image').live("click", function(event) {
                        event.preventDefault();
                        var id = $(this).attr("id");
                        var imageId = "#image-" +id ;
                        $(imageId).find('.options').toggle();
                        $(imageId).addClass("whiteSmoke");


                    });

                    /*

                    $(".stackImage").live("click",function(event) { 
                        event.preventDefault();
                        var id = $(this).attr("id");
                        var imageId = "#" +id ;
                        alert(imageId);
                        $(imageId).append(this.imageSelectedDiv);

                    });

                    $(".stackImage").live("mouseenter",function() {
                        $(this).addClass("whiteSmoke");
                    });

                    $(".stackImage").live("mouseleave",function() {
                        $(this).removeClass("whiteSmoke");
                    }); */

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
