<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use \com\indigloo\sc\Constants as AppConstants;
    use \com\indigloo\Logger as Logger ;


    class Activity {

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
                try{
                    //create object out of string
                    $feedObj = json_decode($feed);
                    if(!property_exists($feedObj, 'type')) {
                        throw new \Exception("bad feed : missing type information");
                    }

                    $feedObj->type = trim($feedObj->type);
                    $processor = new feed\PostProcessor();
                    if($feedObj->type == AppConstants::FOLLOW_FEED) {
                        $processor = new feed\GraphProcessor();
                    }
                    
                    $html .= $processor->process($feedObj);

                } catch(\Exception $ex) {
                    $html .= "error parsing feed: ".$feed ;
                }
            }

            return $html ;

        }

        function getEmailData($feed) {

            $feedText = NULL ;
            $feedHtml = NULL ;
            $processor = NULL ;
            $data = array();

            $feedObj = json_decode($feed);

            if($feedObj === FALSE || $feedObj ===  TRUE || $feedObj == NULL ) {
                //decoding failed.
                return $data ;
            }

            $processor1 = new \com\indigloo\sc\html\feed\PostProcessor();
            $processor2 = new \com\indigloo\sc\html\feed\GraphProcessor();
            $processor3 = new \com\indigloo\sc\html\feed\TextProcessor();

            $templates = array(
                        AppConstants::BOOKMARK_FEED => "/fragments/feed/email/post.tmpl",
                        AppConstants::COMMENT_FEED => "/fragments/feed/email/comment.tmpl",
                        AppConstants::POST_FEED => "/fragments/feed/email/post.tmpl",
                        AppConstants::FOLLOW_FEED => "/fragments/feed/email/vanilla.tmpl");

            $mapHtmlProcessor = array(AppConstants::FOLLOW_FEED => $processor2,
                                    AppConstants::COMMENT_FEED => $processor1,
                                    AppConstants::BOOKMARK_FEED => $processor1,
                                    AppConstants::POST_FEED => $processor1);

            $mapTextProcessor = array(AppConstants::FOLLOW_FEED => $processor3,
                                    AppConstants::COMMENT_FEED => $processor3,
                                    AppConstants::BOOKMARK_FEED => $processor3,
                                    AppConstants::POST_FEED => $processor3);



            $processor = $mapHtmlProcessor[$feedObj->type];
            $html = $processor->process($feedObj,$templates);
            
            $processor = $mapTextProcessor[$feedObj->type];
            $text = $processor->process($feedObj);
            $data["text"] = $text ;
            $data["html"] = $html ;
            return $data ;

        }


    }

}

?>
