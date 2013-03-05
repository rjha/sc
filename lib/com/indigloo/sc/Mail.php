<?php
namespace com\indigloo\sc {

    use com\indigloo\Util as Util ;
    use com\indigloo\Configuration as Config ;

    class Mail {

        /**
         * @return code - returned from sendgrid mail wrapper. 
         * A non zero code indicate failure, zero means success 
         *
         */
        static function sendResetPassword($name,$email,$token) {
            $templates = \com\indigloo\sc\html\Mail::getResetPassword($name,$email,$token);
            $text = $templates["text"];
            $html = $templates["html"];
            $subject = Config::getInstance()->get_value("reset.password.subject");
            $from = Config::getInstance()->get_value("default.mail.address");
            $fromName = Config::getInstance()->get_value("default.mail.name");

            $tos = array($email);
            $code = \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
            return $code ;
        }

        /**
         * @return code - returned from sendgrid mail wrapper. 
         * A non zero code indicate failure, zero means success 
         *
         */
        static function sendActivityMail($name,$email,$feedText,$feedHtml) {
            $templates = \com\indigloo\sc\html\Mail::getActivity($name,$feedText,$feedHtml);
            //get new text and html now.
            $text = $templates["text"];
            $html = $templates["html"];

            // According to mail chimp research - mail  subject 
            // should be 50 chars or less
            // However we need to send long post titles in subject.

            $subject = Util::abbreviate("3mik.com - ".$feedText,100);
            $subject .= "..." ;

            $from = Config::getInstance()->get_value("default.mail.address");
            $fromName = Config::getInstance()->get_value("default.mail.name");

            $tos = array($email);
            $code = \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
            return $code ;
        }

        static function newAccountMail($name,$email) {
            
            $templates = \com\indigloo\sc\html\Mail::getNewAccount($name);
            $text = $templates["text"];
            $html = $templates["html"];
            $subject = Config::getInstance()->get_value("new.account.subject");
            $from = Config::getInstance()->get_value("default.mail.address");
            $fromName = Config::getInstance()->get_value("default.mail.name");

            $tos = array($email);
            $code = \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
            return $code ;
        }

    }

}

?>
