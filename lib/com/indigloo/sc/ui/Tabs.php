<?php

namespace com\indigloo\sc\ui {

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use com\indigloo\Template as Template;

    class Tabs {
        
        static function getHtml($tabUrlMap) {
            $currentUrl = Url::current() ;
            $pos = strpos($currentUrl, '?');

            //remove the part after ? from Url
            if($pos !== false) {
                $currentUrl = substr($currentUrl,0,$pos);
            }

            $records = array();
            foreach($tabUrlMap as $url => $name) {
                $record = array();
                $record["name"] = $name ;
                $record["url"] = $url ;
                $record["class"] = (strcmp($currentUrl,$url) == 0 ) ? "active" : "" ;
                $records[] = $record ;
            }

            $view = new \stdClass;
            $view->records = $records ;
            $template = "/fragments/common/tabs.tmpl"  ;
            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>
