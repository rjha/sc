<?php
namespace com\indigloo\sc\controller{

    
    class Http404 {
        
        function process() {
            header("HTTP/1.1 404 Not Found");
            $message = " <h1> Not found </h1> " ;
            $message .= " The requested URL " .$_SERVER['REQUEST_URI'] ;
            $message .= " was not found on this server";
            echo  $message;
        }
    }
}
?>
