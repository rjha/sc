<?php

namespace com\indigloo\sc\dao {

    
    use com\indigloo\Util as Util ;
    use com\indigloo\sc\mysql as mysql;
    
    class Mail {

        function addResetPassword($name,$email) {
            $token = Util::getMD5GUID();
            $code = mysql\Mail::addResetPassword($name,$email,$token);
            return $code ;
        }
        
    }

}

?>
