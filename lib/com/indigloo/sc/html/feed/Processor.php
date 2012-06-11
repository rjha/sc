<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;

    class Processor {
        private $map ;

        function __construct() {

            $this->map = array(AppConstants::COMMENT_VERB => 'commented on',
                AppConstants::FAVORITE_VERB => 'saved',
                AppConstants::FOLLOWING_VERB => 'is following',
                AppConstants::LIKE_VERB => 'likes',
                AppConstants::POST_VERB => "posted");
        }

        function process($feedObj) {
            $html = '' ;
            $keys = array("subject","subjectId","object","objectId");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if($flag){
                $view['subject'] = $feedObj->subject ;
                $view['object'] = $feedObj->object ;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("/pub/user/%s",$pubId);
                $pubId = PseudoId::encode($feedObj->objectId);
                $view['objectUrl'] = sprintf("/pub/user/%s",$pubId);
                $view['verb'] = $this->getVerb($feedObj->verb);

                $template = '/fragments/feed/vanilla.tmpl' ;
                $html = Template::render($template,$view);

            }

            return $html ;

        }

        function getVerb($verb) {
            return $this->map[$verb];
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


    }

}
?>
