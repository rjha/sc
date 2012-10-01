<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;

    class Processor {
        private $map ;

        function __construct() {

            $this->map = array(AppConstants::COMMENT_VERB => 'commented on',
                AppConstants::SAVE_VERB => 'saved',
                AppConstants::FOLLOWING_VERB => 'is following',
                AppConstants::LIKE_VERB => 'likes',
                AppConstants::POST_VERB => "posted");
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
