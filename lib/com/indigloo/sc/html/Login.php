<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    
    class Login {

        static function get($row) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/ui/login/view.tmpl' ;
            $view->name = $row['name'];
            $view->provider = $row['provider'];
            $view->createdOn = Util::formatDBTime($row['created_on']);
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getList($rows) {
            $html = NULL ;
            $template = '/fragments/ui/login/list.tmpl' ;
            $html = Template::render($template,$rows);
            return $html ;
        }
 

    }
}

?>
