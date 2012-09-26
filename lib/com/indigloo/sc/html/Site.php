<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use \com\indigloo\Constants as Constants ;
    use com\indigloo\Util as Util ;

    use \com\indigloo\util\StringUtil as StringUtil ;

    class Site {

        static function getOverlay($message) {
            if(empty($message)) { return NULL ; }
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/site/overlay.tmpl" ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getNoResult($message) {
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/site/noresult/vanilla.tmpl" ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getNoResultTile($message) {
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/site/noresult/tile.tmpl" ;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;

        }

        static function renderAddBox() {
            $html = NULL ;
            $view = new \stdClass;
            $template = "/fragments/ui/add-box.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function renderSlugPanel($dbslug) {

            $records = array();
            //explode DB slug on space
            $slugs = explode(Constants::SPACE,$dbslug);
            //sort slugs on alpha
            sort($slugs);

            foreach($slugs as $slug) {
                if(Util::tryEmpty($slug)) { continue ; }
                $slug = trim($slug);
                $name =  StringUtil::convertKeyToName($slug);
                $records[] = array("slug" => $slug, "name" => $name ,"checked" => "checked") ;

            }

            $view = new \stdClass;
            $template = "/fragments/ui/slug-panel.tmpl" ;

            $view->records  = $records ;
            $view->total = sizeof($records);

            $html = Template::render($template,$view);
            return $html ;
        }



    }

}

?>
