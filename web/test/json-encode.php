<?php

    include("sc-app.inc");
    include(APP_CLASS_LOADER);

    use \com\indigloo\Util as Util  ;

    function form_safe_json($json) {
        $json = empty($json) ? '[]' : $json ;
        $search = array('\\',"\n","\r","\f","\t","\b","'") ;
        $replace = array('\\\\',"\\n", "\\r","\\f","\\t","\\b", "&#039");
        $json = str_replace($search,$replace,$json);
        return $json;
    }


    $title = "Tiger's   /new \\found \/freedom " ;
    $description = <<<END
    Tiger was caged
    in a Zoo 
    And now he is in jungle
    with freedom
END;

    $book = new \stdClass ;
    $book->title = $title ;
    $book->description = $description ;
    $strBook = json_encode($book);
    $strBook = form_safe_json($strBook);




?>

<!DOCTYPE html>
<html>

    <head>
        <title> title</title>

        <meta charset="utf-8">


        <script type="text/javascript" src="/3p/jquery/jquery-1.7.1.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){
                var strBookObj = '<?php echo $strBook; ?>' ;
                try{
                    bookObj = JSON.parse(strBookObj) ;
                    console.log(bookObj.title);
                    console.log(bookObj.description);
                    $("#title").html(bookObj.title);
                    $("#description").html(bookObj.description);
                } catch(ex) {
                    console.log("Error parsing book object json");
                }

            });
        </script>

    </head>

     <body>

         <h2> Json parsing test page </h2>
         <div id="title"> </div>
         <div id="description"> </div>
    </body>
</html>
