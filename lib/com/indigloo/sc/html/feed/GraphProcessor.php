<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\Url as Url ;

    class GraphProcessor extends Processor{

        function __construct() {
            parent::__construct();
        }

        function process($feedObj,$templates=array()) {
            $html = '' ;
            $keys = array("subject","subjectId","object","objectId");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if(empty($templates)) {
                $templates = array(AppConstants::FOLLOW_FEED => "/fragments/feed/vanilla.tmpl");
            }

            if($flag){
                $view['subject'] = $feedObj->subject ;
                $view['object'] = $feedObj->object ;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("%s/pub/user/%s",Url::wwwBase(),$pubId);
                $pubId = PseudoId::encode($feedObj->objectId);
                $view['objectUrl'] = sprintf("%s/pub/user/%s",Url::wwwBase(),$pubId);
                $view['verb'] = $this->getVerb($feedObj->verb);

                if(isset($templates[$feedObj->type])) {
                    $template = $templates[$feedObj->type];
                } else {
                    trigger_error("invalid feed template", E_USER_ERROR);
                }

                $html = Template::render($template,$view);
            }

            return $html ;

        }

    }

}
?>
