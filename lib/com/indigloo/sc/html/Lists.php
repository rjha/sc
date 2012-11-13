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
            $template =  "/fragments/lists/dash/summary/image.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function createListView($row) {
            $view = new \stdClass ;

            $view->id = $row["id"];
            $view->name = $row["name"];
            $view->items = json_decode($row["items_json"]);
            $view->count = $row["item_count"] ;
            $view->hasImage = false ;

            foreach($view->items as $item){
                $view->srcImage = $item->thumbnail ;
                $view->hasImage = true ;
                break ;
            }

            return $view ;

        }
    }

}

?>
