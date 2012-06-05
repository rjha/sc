<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\sc\Constants as AppConstants ;

    class ActivityFeed {

        private $map ;

        function __construct() {

            $this->map = array(AppConstants::COMMENT_VERB => 'commented on',
                AppConstants::FAVORITE_VERB => 'saved',
                AppConstants::FOLLOWING_VERB => 'is following',
                AppConstants::LIKE_VERB => 'likes',
                AppConstants::POST_VERB => "posted");
        }

        function checkKeys($feedObj,$keys) {
            $flag = true ;

            foreach($keys as $key) {
                if(!property_exists($feedObj,$key)) {
                    $flag = false ;
                    return ;
                }
            }
            //success
            return $flag ;
        }

        function getHtml($feedDataObj) {

            //dataObj is NULL or empty for error case
            if(empty($feedDataObj)) {
                $html =  "Error retrieving activity data!" ;
                return $html;
            }

            if(!property_exists($feedDataObj, "feeds")) {
                $html =  "Malformed feed object : missing feeds!" ;
                return $html;
            }

            if(property_exists($feedDataObj, "error")) {
                $html = $feedDataObj->error ;
                return $html ;
            }

            //dataObj->feeds is always an array
            $html = '' ;
            //placeholder for transformed feeds
            $rows = array();

            $index = 0 ;
            foreach($feedDataObj->feeds as $feed) {
                $index++ ;
                //create object out of string
                $feedObj = json_decode($feed);
                if(!property_exists($feedObj, 'type')) {
                    trigger_error("feed is missing type information", E_USER_ERROR);
                }

                $record = array();

                if(strcmp(trim($feedObj->type), AppConstants::FOLLOW_FEED) == 0 ) {
                    $keys = array("subject","subjectId","object","objectId");
                    $flag = $this->checkKeys($feedObj,$keys);
                    if($flag){
                        $record['subject'] = $feedObj->subject ;
                        $record['object'] = $feedObj->object ;
                        $pubId = PseudoId::encode($feedObj->subjectId);
                        $record['subjectUrl'] = sprintf("/pub/user/%s",$pubId);
                        $pubId = PseudoId::encode($feedObj->objectId);
                        $record['objectUrl'] = sprintf("/pub/user/%s",$pubId);
                    }

                }

                if( (strcmp(trim($feedObj->type), AppConstants::BOOKMARK_FEED) == 0)
                        || (strcmp(trim($feedObj->type), AppConstants::POST_FEED) == 0)
                        || (strcmp(trim($feedObj->type), AppConstants::COMMENT_FEED) == 0)) {

                    $keys = array("subject","subjectId","title","objectId");
                    $flag = $this->checkKeys($feedObj,$keys);
                    if($flag){
                        $record['subject'] = $feedObj->subject;
                        $record['object'] = $feedObj->title;
                        $pubId = PseudoId::encode($feedObj->subjectId);
                        $record['subjectUrl'] = sprintf("/pub/user/%s", $pubId);
                        $record['objectUrl'] = sprintf("/item/%s", $feedObj->objectId);
                    }
                }

                if(!empty($record)){
                    //add verb push new record
                    $record['verb'] = $this->map[$feedObj->verb];
                    //LINDEX <key> <index>
                    $record['index'] = $index - 1;
                    $rows[] = $record ;
                }

            }

            //format records for display
            $view = new \stdClass ;
            $view->rows = $rows ;
            $template = '/fragments/activity/feed.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;

        }



    }

}

?>
