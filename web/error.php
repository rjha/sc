<?php

    if(isset($_GET['debug']) && ($_GET['debug'] == '1')){
        $error = $_GET['message'];
        $error = base64_decode($error);
    } else {
        $error = '' ; //donot show
    }
    
?>
<!DOCTYPE html>
<html>  
    <head>
        
        <title>3mik.com - Error Page </title>
        
        <style type="text/css">
        
             body {
                text-align: center;
                color: black ;
            }
            
            .centered {
                position: fixed;
                top: 50%;
                left: 50%;
                margin-top: -175px;
                margin-left: -230px;
                font-family : Arial, sans-serif ;
                font-size : 13px;
            }

            #mini_inner {
                background-color: whiteSmoke;
                width:400px ;
                padding: 50px;
                /* height:225px; */
                border-radius: 10px;
                z-index:1;
            }

			#mini_inner a {
				text-decoration : none ;

			}
           
            .error{
                display :block ;
                color :red ;
            }


        </style>

    </head>
    <body>
        <div id="mini_inner" class="centered">
            <img src="/css/images/alert.png" alt="alert" class="alert">
            <h1> Error! We Apologize.</h1>
            <p>This page has encountered an error </p>
			<div class="error"> <?php echo $error ; ?> </div>
			<br>
			<p> To know more you can examine the logs or contact your administrator. </p>
			<br>
			Go back to  <a href="/"> Home page</a>
        </div>



    </body>

</html>




