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

        static function getAdminWidget($index,$content) {

            $html = NULL ;
            $view = new \stdClass;
            $view->index = $index ;
            $view->content = $content ;
            $template = '/fragments/feed/admin/widget.tmpl' ;
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
                if(!property_exists($feedObj, "verb")) {
                    trigger_error("bad feed: verb is missing from feed",E_USER_ERROR);
                }

                //ignore comments for a post
                if($feedObj->verb != AppConstants::COMMENT_VERB) {
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

            $processor1 = new feed\PostProcessor();
            $processor2 = new feed\GraphProcessor();
            $processor = NULL ;

            foreach($feedDataObj->feeds as $feed) {

                try{
                    //create object out of string
                    $feedObj = json_decode($feed);
                    if(!property_exists($feedObj, "verb")) {
                        throw new \Exception("bad feed: verb is missing from feed!");
                    }

                    $processor = 
                    ($feedObj->verb == AppConstants::FOLLOW_VERB) ? $processor2 : $processor1 ;
                    $html .= $processor->process($feedObj);

                } catch(\Exception $ex) {
                    $html .= "error parsing feed: ".$feed ;
                }
            }

            if(empty($html)) {
                $html = "No data found!" ;
            }

            return $html ;

        }

        function getEmailData($feed) { 

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
                        AppConstants::LIKE_VERB => "/fragments/feed/email/post.tmpl",
                        AppConstants::COMMENT_VERB => "/fragments/feed/email/comment.tmpl",
                        AppConstants::POST_VERB => "/fragments/feed/email/post.tmpl",
                        AppConstants::FOLLOW_VERB => "/fragments/feed/email/vanilla.tmpl");

            $mapHtmlProcessor = array(AppConstants::FOLLOW_VERB => $processor2,
                                    AppConstants::COMMENT_VERB => $processor1,
                                    AppConstants::LIKE_VERB => $processor1,
                                    AppConstants::POST_VERB => $processor1);



            $processor = $mapHtmlProcessor[$feedObj->verb];
            $html = $processor->process($feedObj,$templates);
            $text = $processor3->process($feedObj);

            $data["text"] = $text ;
            $data["html"] = $html ;
            return $data ;

        }

    }

}

?>
