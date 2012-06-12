<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use \com\indigloo\sc\Constants as AppConstants;

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
            $html = '' ;

            $error = $this->errorCheck($feedDataObj);
            if(!is_null($error)) {
                return $error ;
            }

            foreach($feedDataObj->feeds as $feed) {

                //create object out of string
                $feedObj = json_decode($feed);
                if(!property_exists($feedObj, 'type')) {
                    trigger_error("feed is missing type information", E_USER_ERROR);
                }

                $feedObj->type = trim($feedObj->type);

                //ignore comments for a post
                if($feedObj->type != AppConstants::COMMENT_FEED) {
                    //get basic feed processor
                    $processor = new feed\ItemProcessor();
                    $options = array();
                    $html .= $processor->process($feedObj,$options);
                }

            }

            return $html ;
        }

        function errorCheck($feedDataObj) {
            $error = NULL ;
            //dataObj is NULL or empty for error case
            if(empty($feedDataObj)) {
                $error =  "Error retrieving activity data!" ;
                return $error;
            }

            if(!empty($feedDataObj) && (property_exists($feedDataObj, "error"))) {
                $error = $feedDataObj->error ;
                return $error ;
            }

            // this should never happen
            if(!property_exists($feedDataObj, "feeds")) {
                $error =  "Malformed feed object : missing feeds!" ;
                return $error;
            }
        }

        function getHtml($feedDataObj) {

            $html = '' ;
            $error = $this->errorCheck($feedDataObj);
            if(!is_null($error)) {
                return $error ;
            }

            foreach($feedDataObj->feeds as $feed) {

                //create object out of string
                $feedObj = json_decode($feed);
                if(!property_exists($feedObj, 'type')) {
                    trigger_error("feed is missing type information", E_USER_ERROR);
                }

                $feedObj->type = trim($feedObj->type);
                $processor = new feed\PostProcessor();
                if($feedObj->type == AppConstants::FOLLOW_FEED) {
                    $processor = new feed\GraphProcessor();
                }

                $html .= $processor->process($feedObj);

            }

            return $html ;

        }

    }

}

?>
