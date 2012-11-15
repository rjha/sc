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
            
            $params = array("id" => $row['id'], "q" => base64_encode(Url::current()) );
            $view->deleteUrl = Url::createUrl("/monitor/feedback/delete.php",$params);
            
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
