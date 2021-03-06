<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\sc\view\Media as MediaView ;
    use \com\indigloo\Util as Util ;
    
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    
    use \com\indigloo\sc\Constants as AppConstants ;
    use \com\indigloo\sc\Util as AppUtil ;


    class Comment {

        static function getSummary($row) {
            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/comment/summary.tmpl' ;

            $view->comment = $row['description'];
            $view->createdOn = AppUtil::convertDBTime($row['created_on']);
            $view->userName = $row['user_name'] ;
            $view->loginId = $row['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getWidget($row,$options=NULL) {

            $html = NULL ;
            $view = new \stdClass;
            $template = '/fragments/comment/text.tmpl' ;

            if(is_null($options)) {
                $options = ~UIConstants::COMMENT_ALL ;
            }

            $view->id = $row['id'];

            $view->title = $row['title'];
            $view->postId = $row['post_id'];
            $view->itemId = PseudoId::encode($view->postId);

            $view->comment = $row['description'];
            $view->createdOn = AppUtil::convertDBTime($row['created_on']);
            $view->showUser = false ;

            if($options & UIConstants::COMMENT_USER) {
                $view->loginId = $row['login_id'];
                $view->pubUserId = PseudoId::encode($view->loginId);
                $view->userName = $row['user_name'] ;
                $view->showUser = true ;
            }

            $encodedId = PseudoId::encode($view->id);
            $params = array('id' => $encodedId, 'q' => base64_encode(Url::current()));
            $view->editUrl = Url::createUrl('/qa/comment/edit.php',$params);
            $view->deleteUrl = Url::createUrl('/qa/comment/delete.php',$params);

            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>
