<?php

namespace com\indigloo\sc\dao {

    
    use com\indigloo\Util as Util ;
    use com\indigloo\sc\mysql as mysql;
    use com\indigloo\exception\DBException as DBException;
    
    class Mail {

        function addResetPassword($name,$email) {
            $token = Util::getMD5GUID();
            //do we have a request pending already?
            $code = mysql\Mail::addResetPassword($name,$email,$token);
            if($code != 0 ) {
                $message = sprintf("DB Error : code  %d ",$code);
                throw new DBException($message,$code);
            }
        }
        
    }

}

?>
