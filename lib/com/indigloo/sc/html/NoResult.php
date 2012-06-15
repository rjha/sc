<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\ui\Constants as UIConstants ;

    class NoResult {

        static function get($message) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/ui/message/noresult.tmpl' ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getTile($message) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/ui/tile/noresult.tmpl' ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;

        }

    }

}

?>
