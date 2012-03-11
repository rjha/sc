<?php

function webgloo_error_handler($errorno,$errorstr,$file,$line) {

    if(error_reporting() == 0 ) {
        // do nothing for silenced errors
        return true ;
    }
    
    switch($errorno) {

        case E_STRICT :
            //do nothing
            // PEAR packages use PHP-4 compatible code that throws
            // do not log E_STRICT errors - Better get rid of PEAR packages
            // turn on logger when debugging issues
            return true;
        case E_NOTICE :
        case E_USER_NOTICE :
            com\indigloo\Logger::getInstance()->error(" $file :: $line :: $errorstr");
            break ;

        //E_USER_ERROR are not necessarily well defined errors
        //so we cannot just display back those errors
        //Also we cannot assume that loggin has been done.
        //so we just let it be the default error case!
        case E_USER_ERROR:
        default:
            ob_start();
            debug_print_backtrace();
            $trace = ob_get_contents();
            @ ob_end_clean();
            //debug_print_backtrace();
            com\indigloo\Logger::getInstance()->trace($file,$line,$errorstr,$trace);
            //send generic json encoded message back to UI
            // 302 to error page
            $errorPageUrl = "/error.php?message=".base64_encode($errorstr) ;
            header('Location: '.$errorPageUrl);
            
            //Imp:Finally error handler should die
            exit(1);
            
    }
    
    //do not execute PHP error handler
    return true ;
}

function webgloo_ajax_error_handler($errorno,$errorstr,$file,$line) {

    if(error_reporting() == 0 ) { return true ; }

    switch($errorno) {
        case E_STRICT :
            // do not log E_STRICT errors - Better get rid of PEAR packages
            // turn on logger when debugging issues
            return true;
        case E_NOTICE :
        case E_USER_NOTICE :
            com\indigloo\Logger::getInstance()->error(" $file :: $line :: $errorstr");
            break ;

        case E_USER_ERROR:
        default:
            ob_start();
            debug_print_backtrace();
            $trace = ob_get_contents();
            @ ob_end_clean();
            com\indigloo\Logger::getInstance()->trace($file,$line,$errorstr,$trace);
            //send ajax error message back to UI
            $message =  'Internal server error happened. Please check log for details!';
            $error = array('code' => 500, 'message' => $message);
            $message =  json_encode($error) ;
            echo $message ;
            //Imp:Finally error handler should die
            exit(1);
    }
    
    return true ;
}

ob_start();
//set default error handler
set_error_handler('webgloo_error_handler');

?>