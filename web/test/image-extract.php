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
            .green { background: whiteSmoke ; }


        </style>

        <script type="text/javascript">

            var imagePreviewDiv = '<div class="stackImage" >' 
                + ' <img src="{srcImage}" class="thumbnail-1" />  </div>' ;

            function addImage(image) {
                console.log(image);
                var buffer = imagePreviewDiv.supplant({"srcImage" : image } );
                console.log(buffer);
                $("div#image-data").append(buffer);

            }

            $(document).ready(function(){
                $(".stackImage").live("click",function(event) { 
                    event.preventDefault();
                    alert("click");
                    $(this).addClass("green");

                });

                $(".stackImage").live("mouseenter",function() {
                    $(this).addClass("yellow");

                });

                $(".stackImage").live("mouseleave",function() {
                    $(this).removeClass("yellow");
                
                });

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
                    for(i = 0 ; i < images.length ; i++) {
                        addImage(images[i]);
                    }

                } catch(ex) {
                    console.log("Error parsing response object json");
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
