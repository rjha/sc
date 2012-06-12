<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;

    class TextProcessor extends Processor{

       function __construct() {
            parent::__construct();
        }

        function process($feedObj) {
            $text = '' ;
            $keys = array("subject","object");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if($flag){
                $view['subject'] = $feedObj->subject ;
                $title = $feedObj->title ;
                if($feedObj->type == AppConstants::FOLLOW_FEED) {
                    $title = $feedObj->object ;
                }
                
                $view['object'] = $feedObj->title ;
                $view['verb'] = $this->getVerb($feedObj->verb);

                $template = '/fragments/feed/text/vanilla.tmpl' ;
                $text = Template::render($template,$view);

            }

            return $text ;

        }

    }

}
?>
