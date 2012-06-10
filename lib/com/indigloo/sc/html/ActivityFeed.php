<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;

    class ActivityFeed {

        function getHomeTile($feedDataObj) {
            $content = $this->getHtml($feedDataObj);
            $html = NULL ;
            $view = new \stdClass;
            $view->content = $content ;
            $template = '/fragments/feed/tile.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
        }

        function getPostTile($feedDataObj) {
            $content = $this->getHtml($feedDataObj);
            return $content ;
        }

        function getHtml($feedDataObj) {

            $html = '' ;
            //dataObj is NULL or empty for error case
            if(empty($feedDataObj)) {
                $html =  "Error retrieving activity data!" ;
                return $html;
            }

            if(!empty($feedDataObj) && (property_exists($feedDataObj, "error"))) {
                $html = $feedDataObj->error ;
                return $html ;
            }

            // this should never happen
            if(!property_exists($feedDataObj, "feeds")) {
                $html =  "Malformed feed object : missing feeds!" ;
                return $html;
            }
            
            foreach($feedDataObj->feeds as $feed) {

                //create object out of string
                $feedObj = json_decode($feed);
                if(!property_exists($feedObj, 'type')) {
                    trigger_error("feed is missing type information", E_USER_ERROR);
                }

                $feedObj->type = trim($feedObj->type);
                //get feed processor
                $processor = feed\ProcessorFactory::get($feedObj->type);
                $options = array();
                $html .= $processor->process($feedObj,$options);

            }

            return $html ;

        }

    }

}

?>