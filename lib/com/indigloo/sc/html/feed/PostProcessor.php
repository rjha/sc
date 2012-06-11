<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;

    class PostProcessor extends Processor{

        function __construct() {
            parent::__construct();
        }
        
        function process($feedObj) {
            $html = '' ;
            $keys = array("subject","subjectId","title","objectId");
            $flag = $this->checkKeys($feedObj,$keys);
            $view = array();

            if($flag){
                $view['subject'] = $feedObj->subject;
                $view['object'] = $feedObj->title;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("/pub/user/%s", $pubId);
                $view['objectUrl'] = sprintf("/item/%s", $feedObj->objectId);
                $view['srcImage'] = $feedObj->srcImage ;
                $view['nameImage'] = $feedObj->nameImage ;
                $view['verb'] = $this->getVerb($feedObj->verb);

                $template = '/fragments/feed/image/post.tmpl' ;
                
                 //extra processing for posts.
                if(strcmp($feedObj->type,AppConstants::COMMENT_FEED) == 0 ) {
                    $template = '/fragments/feed/image/comment.tmpl' ;
                    if(property_exists($feedObj, 'content')) {
                        $view['content'] = $feedObj->content ;
                    }
                }
                
                //extra processing for comments.
                if(strcmp($feedObj->type,AppConstants::COMMENT_FEED) == 0 ) {
                    $template = '/fragments/feed/image/comment.tmpl' ;
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
