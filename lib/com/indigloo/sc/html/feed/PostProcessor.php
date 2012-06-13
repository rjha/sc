<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\Url as Url ;

    class PostProcessor extends Processor{

        function __construct() {
            parent::__construct();
        }

        function process($feedObj,$templates=array()) {
            $html = '' ;
            $keys = array("subject","subjectId","title","objectId");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if(empty($templates)) {
                $templates = array(
                    AppConstants::BOOKMARK_FEED => "/fragments/feed/image/post.tmpl",
                    AppConstants::COMMENT_FEED => "/fragments/feed/image/comment.tmpl",
                    AppConstants::POST_FEED => "/fragments/feed/image/post.tmpl",
                    AppConstants::FOLLOW_FEED => NULL);
            }

            if($flag){
                $view['subject'] = $feedObj->subject;
                $view['object'] = $feedObj->title;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("%s/pub/user/%s",Url::wwwBase(),$pubId);
                $view['objectUrl'] = sprintf("%s/item/%s",Url::wwwBase(),$feedObj->objectId);
                $view['srcImage'] = $feedObj->srcImage ;
                $view['nameImage'] = $feedObj->nameImage ;
                $view['verb'] = $this->getVerb($feedObj->verb);
                
                if(isset($templates[$feedObj->type])) {
                    $template = $templates[$feedObj->type];
                } else {
                    trigger_error("invalid feed template", E_USER_ERROR);
                }

                //extra processing for comments.
                if(strcmp($feedObj->type,AppConstants::COMMENT_FEED) == 0 ) {
                    if(property_exists($feedObj, 'content')) {
                        $view['content'] = $feedObj->content ;
                    }
                }

                $html = Template::render($template,$view);
            }

            return $html ;

        }

    }

}
?>
