<?php
namespace com\indigloo\sc\controller{


    class Http400 {

        function process() {
            header("HTTP/1.1 400 Bad Request");
            $message = " <h1> Bad Request </h1> " ;
            $message .= " The requested URL " .$_SERVER['REQUEST_URI'] ;
            $message .= " is malformed";
            echo  $message;
        }
    }
}
?>
