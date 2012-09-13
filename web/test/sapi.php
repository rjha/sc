<?php


switch(PHP_SAPI) {
    case 'cli' :
        printf("sapi is cli \n");
    break;
    default:
        echo "sapi is cgi <br>" ;
    break;
}



?>
