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
            $tos = array($email);
            \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$subject,$text,$html);
        }

        static function sendActivityMail($name,$email,$feedText,$feedHtml) {
            $templates = \com\indigloo\sc\html\Mail::getActivity($name,$feedText,$feedHtml);
            //get new text and html now.
            $text = $templates["text"];
            $html = $templates["html"];

            // According to mail chimp research
            //subject should be 50 chars or less
            // however we need to send long post titles in subject.

            $subject = Util::abbreviate("3mik.com - ".$feedText,100);
            $subject .= "..." ;
            
            $from = Config::getInstance()->get_value("default.mail.address");
            $tos = array($email);
            \com\indigloo\mail\SendGrid::sendViaWeb($tos,$from,$subject,$text,$html);
        }

    }

}

?>
