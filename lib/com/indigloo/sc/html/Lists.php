<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;

    class Lists {

        static function getSelectPopup($listRows,$qUrl) {
            $view = new \stdClass;
            $template =  "/fragments/lists/popup/select.tmpl" ;

            $view->lists = array();
            $view->size = sizeof($listRows) ;

            foreach($listRows as $row) {
                $view->lists[$row['id']] = $row['name'] ;
            }
            
            $view->qUrl = $qUrl;
            $html = Template::render($template,$view);
            return $html ;
        }
    }

}

?>
