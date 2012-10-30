<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;

    class Lists {

        static function getSelectPopup($listRows,$qUrl) {
            $view = new \stdClass;
            $template =  (sizeof($listRows) > 0) ? "/fragments/lists/popup/select.tmpl" : "/fragments/lists/popup/select-0.tmpl"  ;

            $view->lists = array();
            $view->size = sizeof($listRows) ;

            foreach($listRows as $row) {
                $view->lists[$row['id']] = $row['name'] ;
            }
            
            $view->qUrl = $qUrl;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getWidget($listDBRow) {
            $view = self::createListView($listDBRow);
            $template =  "/fragments/lists/user/widget.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function createListView($listDBRow) {
            $view = new \stdClass ;

            $view->id = $listDBRow["id"];
            $view->name = $listDBRow["name"];
            $view->items = json_decode($listDBRow["items_json"]);

            return $view ;

        }
    }

}

?>
