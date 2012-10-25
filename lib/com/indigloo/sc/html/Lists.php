<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;

    class Lists {

        static function getSelectPopup($listRows,$strItems,$qUrl) {
            $view = new \stdClass;
            $template = (sizeof($listRows) > 0 ) ? 
                "/fragments/lists/popup/select.tmpl" : "/fragments/lists/popup/select0.tmpl" ;

            $view->lists = array();
            foreach($listRows as $row) {
                $view->lists[$row['id']] = $row['name'] ;
            }
            
            $view->strItems = $strItems ;
            $view->qUrl = $qUrl;
            $html = Template::render($template,$view);
            return $html ;
        }
    }

}

?>
