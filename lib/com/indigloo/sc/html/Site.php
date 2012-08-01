<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;

    class Site {

        static function getOverlay($message) {
            if(empty($message)) { return NULL ; }
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/site/overlay.tmpl' ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }
    }

}

?>
