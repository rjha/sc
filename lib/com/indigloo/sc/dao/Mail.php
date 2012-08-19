<?php

namespace com\indigloo\sc\dao {


    use \com\indigloo\Util as Util ;
    use \com\indigloo\sc\mysql as mysql;
    use \com\indigloo\exception\UIException as UIException;

    class Mail {

        function checkResetPassword($email,$token) {
            $row = mysql\Mail::getResetPassword($email,$token);
            $count = $row['count'] ;
            if($count < 1 ) {
                $message = "This token has expired. Please submit again.";
                throw new UIException(array($message));
            }
        }

        function addResetPassword($name,$email) {

            //3mik user account exists with this email?
            $row = mysql\User::has3mikEmail($email);
            $count = $row["count"] ;
            if($count <= 0 ) {
                $message = "Sorry! We could not find any 3mik login with this email.";
                throw new UIException(array($message));
            }

            //is a request already pending for this email?
            $row = mysql\Mail::getResetPasswordInRange($email);
            $count = $row["count"] ;
            if($count > 0 ) {
                $message = "Your request is already pending. Please try after 20 minutes.";
                throw new UIException(array($message));
            }


            $token = Util::getMD5GUID();
            mysql\Mail::addResetPassword($name,$email,$token);

            //now send an email
            $code = \com\indigloo\sc\Mail::sendResetPassword($name,$email,$token);
            if($code > 0 ) {
                $message = "There was an error sending mail. Please try again after 20 minutes.";
                throw new UIException(array($message));
            }

            //update flag in DB
            mysql\Mail::flipResetPassword($email);

        }
        
    }

}

?>
