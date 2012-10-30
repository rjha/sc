<?php

namespace com\indigloo\sc\ui {

    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use com\indigloo\Template as Template;

    class Links {
        
        static function getHtml($map,$template,$classMap) {
            $currentUrl = Url::current() ;
            $pos = strpos($currentUrl, '?');

            //remove the part after ? from Url
            if($pos !== false) {
                $currentUrl = substr($currentUrl,0,$pos);
            }

            $records = array();
            foreach($map as $url => $name) {
                $record = array();
                $record["name"] = $name ;
                $record["url"] = $url ;
                $record["class"] = (strcmp($currentUrl,$url) == 0 ) ? $classMap["active"] : $classMap["normal"] ;
                $records[] = $record ;
            }

            $view = new \stdClass;
            $view->records = $records ;
            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>
