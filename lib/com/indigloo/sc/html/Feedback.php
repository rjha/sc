<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;

    class Feedback {

        static function get($row) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/monitor/feedback.tmpl' ;

            $view->id = $row['id'];
            $view->qUrl = base64_encode(Url::current());
            $view->description = $row['feedback'];
            $view->name = $row['name'] ;
            $view->phone = $row['phone'] ;
            $view->email = $row['email'] ;

            $html = Template::render($template,$view);
            return $html ;
        }
    }
}

?>
