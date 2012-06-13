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
            //do we have a request pending already?
            $row = mysql\Mail::getResetPasswordInRange($email);
            $count = $row['count'] ;
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

        function processResetPassword($name,$email,$token) {
            //now send an email
            $code = \com\indigloo\sc\Mail::sendResetPassword($name,$email,$token);
            if($code > 0 ) {
                $message = "There was an error sending mail. Please try again after 20 minutes.";
                throw new UIException(array($message));
            }

            mysql\Mail::flipResetPassword($email);
        }

    }

}

?>
