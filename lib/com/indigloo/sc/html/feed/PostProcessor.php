<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    use \com\indigloo\Template as Template;
    use \com\indigloo\sc\util\PseudoId as PseudoId;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\Configuration as Config ;

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
                // extra processing for comments
                // @imp: activity row for comment stores 
                // post_id as object_id and not item_id
                if(strcmp($feedObj->verb,AppConstants::COMMENT_VERB) == 0 ) {
                    if(property_exists($feedObj, 'content')) {
                        $view['content'] = $feedObj->content ;
                    }
                    $feedObj->objectId = PseudoId::encode($feedObj->objectId);
                }

                $view['subject'] = $feedObj->subject;
                $view['object'] = $feedObj->title;
                $pubId = PseudoId::encode($feedObj->subjectId);
                $view['subjectUrl'] = sprintf("%s/pub/user/%s",Url::base(),$pubId);
                $view['objectUrl'] = sprintf("%s/item/%s",Url::base(),$feedObj->objectId);
                $view['hasImage'] = false ;

                //image for feed
                if(property_exists($feedObj, 'srcImage')) {
                    if(!empty($feedObj->srcImage)) {
                        $srcImage = $feedObj->srcImage ;
                        
                        $m_bucket = \parse_url($srcImage,\PHP_URL_HOST);
                        // aws s3 bucket mapping for cloud front
                        // host is a CNAME mapped to amazon s3 bucket
                        // format is store.bucket.mapto=<mapped-bucket>
                        $mapKey = sprintf("s3.%s.mapto",$m_bucket) ;
                        $bucket = Config::getInstance()->get_value($mapKey,$m_bucket);
                        $view['srcImage'] = str_replace($m_bucket,$bucket,$srcImage);

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

                

                $html = Template::render($template,$view);
                
            }

            return $html ;

        }

    }

}
?>
