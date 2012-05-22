<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Util as Util ;
    use com\indigloo\Template as Template;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;
    
    class GroupPanel {

        /**
         * @param dbslug - space separated group slug as stored in the DB 
         *
         */

        static function render($dbslug){

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
            $template = '/fragments/ui/group/panel.tmpl' ;

            $view->records  = $records ;
            $view->total = sizeof($records);
            $view->step = 11 ;

            $html = Template::render($template,$view);
            return $html ;

        }

    }
    
}

?>
