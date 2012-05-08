<?php
namespace com\indigloo\sc {

    use com\indigloo\Util as Util ;
    use com\indigloo\Configuration as Config ;
    
    class Mail {

        static function sendResetPassword($name,$email,$token) {
            $templates = \com\indigloo\sc\html\Mail::getResetPassword($name,$email,$token);
            $text = $templates['text'];
            $html = $templates['html'];
            $subject = Config::getInstance()->get_value("reset.password.subject");
            $from = Config::getInstance()->get_value("default.mail.address");
            \com\indigloo\mail\SendGrid::sendViaWeb($email,$from,$subject,$text,$html);
        } 

        static function sendNewAccount($name,$email,$password) {
            //@todo
        } 


    }

}

?>