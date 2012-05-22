<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    
    class Mail {

        static function getResetPassword($name,$email,$token) {

            $view = new \stdClass;
            $view->name = $name;
            $url = Config::getInstance()->get_value("reset.password.url");
            $view->url = sprintf($url,urlencode($email),$token);
           
            $template = '/fragments/mail/text/reset-password.tmpl' ;
            $text = Template::render($template,$view);

            $template = '/fragments/mail/html/reset-password.tmpl' ;
            $html = Template::render($template,$view);

            $data = array('text' => $text , 'html' => $html);
            return $data;

        }

        static function getSearchTokens($tokens) {
            $view = new \stdClass;
            
            $content = '' ;
            foreach($tokens as $token) {
                $content .= sprintf(" %s ",$token); 
            }

            $view->content = $content ;

            $template = '/fragments/mail/text/search-tokens.tmpl' ;
            $text = Template::render($template,$view);

            $template = '/fragments/mail/html/search-tokens.tmpl' ;
            $html = Template::render($template,$view);

            $data = array('text' => $text , 'html' => $html);
            return $data;
        }
    }
}
?>
