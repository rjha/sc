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

            $bucket = array();
            $activeName = NULL ;

            foreach($map as $url => $name) {
                //links to store
                if(!array_key_exists($name,$bucket)) {
                    $record = array();
                    $record["name"] = $name ;
                    $record["url"] = $url ;
                    $record["class"] = $classMap["normal"] ;
                    $bucket[$name] = $record ;
                    
                }

                if(strcmp($url, $currentUrl) == 0 ) {
                    //name to highlight
                    $activeName = $name ;
                }
            }

            $bucket[$activeName]["class"] = $classMap["active"];
            
            $view = new \stdClass;
            $view->records = array_values($bucket) ;
            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>
