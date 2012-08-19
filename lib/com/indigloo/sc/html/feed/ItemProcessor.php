<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\Url as Url ;

    class ItemProcessor extends Processor{

       function __construct() {
            parent::__construct();
        }

        function process($feedObj) {
            $html = '' ;
            $keys = array("subject","subjectId","object","objectId");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if($flag){
                $view['subject'] = $feedObj->subject ;
                $view['object'] = "this item" ;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("%s/pub/user/%s", Url::base(),$pubId);

                $view['objectUrl'] = sprintf("%s/item/%s",Url::base(),$feedObj->objectId);
                $view['verb'] = $this->getVerb($feedObj->verb);

                $template = '/fragments/feed/vanilla.tmpl' ;
                $html = Template::render($template,$view);

            }

            return $html ;

        }

    }

}
?>
