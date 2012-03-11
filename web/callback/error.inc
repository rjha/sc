<?php

function login_error_handler($errorno,$errorstr,$file,$line) {

    if(error_reporting() == 0 ) {
        // do nothing for silenced errors
        return true ;
    }
    
    switch($errorno) {

        case E_STRICT :
            return true;
        case E_NOTICE :
        case E_USER_NOTICE :
            \com\indigloo\Logger::getInstance()->error(" $file :: $line :: $errorstr");
            break ;

        case E_USER_ERROR:
            com\indigloo\Logger::getInstance()->trace($file,$line,$errorstr,'TRACE');
			$_SESSION["form.errors"] = array($errorstr);
			header('Location: /user/login.php');
            exit(1);
			

		default:
            com\indigloo\Logger::getInstance()->trace($file,$line,$errorstr,'TRACE');
			$_SESSION["form.errors"] = array("Error happened during login");
			header('Location: /user/login.php');
            exit(1);
            
    }
    
    //do not execute PHP error handler
    return true ;
}

?>
