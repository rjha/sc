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
                    AppConstants::LIKE_VERB => "/fragments/feed/image/post.tmpl",
                    AppConstants::COMMENT_VERB => "/fragments/feed/image/comment.tmpl",
                    AppConstants::POST_VERB => "/fragments/feed/image/post.tmpl",
                    AppConstants::FOLLOW_VERB => NULL);
            }

            if($flag){
                $view['subject'] = $feedObj->subject;
                $view['object'] = $feedObj->title;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("%s/pub/user/%s",Url::base(),$pubId);
                $view['objectUrl'] = sprintf("%s/item/%s",Url::base(),$feedObj->objectId);
                $view['hasImage'] = false ;

                //image for feed
                if(property_exists($feedObj, 'srcImage')) {
                    if(!empty($feedObj->srcImage)) {
                        $view['srcImage'] = $feedObj->srcImage ;
                        $view['nameImage'] = $feedObj->nameImage ;
                        $view['hasImage'] = true ;
                    }
                        
                }
               
                $view['verb'] = $this->getVerb($feedObj->verb);
                
                if(isset($templates[$feedObj->verb])) {
                    $template = $templates[$feedObj->verb];
                } else {
                    trigger_error("invalid feed template", E_USER_ERROR);
                }

                //extra processing for comments.
                if(strcmp($feedObj->verb,AppConstants::COMMENT_VERB) == 0 ) {
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
