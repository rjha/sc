<?php

namespace com\indigloo\sc\dao {

    
    use com\indigloo\Util as Util ;
    use com\indigloo\sc\mysql as mysql;
    use com\indigloo\exception\DBException as DBException;
    
    class Mail {

        function checkResetPassword($email,$token) {
            $row = mysql\Mail::getResetPassword($email,$token);
            $count = $row['count'] ;
            if($count < 1 ) {
                $message = "This token has expired. Please submit again.";
                throw new DBException($message,1);
            }
        }

        function addResetPassword($name,$email) {
            //do we have a request pending already?
            $row = mysql\Mail::getResetPasswordInRange($email);
            $count = $row['count'] ;
            if($count > 0 ) {
                $message = "Your request is already pending. Please try after 20 minutes.";
                throw new DBException($message,1);
            }

            $token = Util::getMD5GUID();
            $code = mysql\Mail::addResetPassword($name,$email,$token);
            if($code != 0 ) {
                $message = sprintf("DB Error : code  %d ",$code);
                throw new DBException($message,$code);
            }

            //now send an email
            \com\indigloo\sc\Mail::sendResetPassword($name,$email,$token);
            //update flag in DB
            mysql\Mail::flipResetPassword($email);

        }

        function processResetPassword($email,$token) {
            //now send an email
            \com\indigloo\sc\Mail::sendResetPassword($name,$email,$token);
            //update flag in DB
            mysql\Mail::flipResetPassword($email);
        }
        
    }

}

?>
