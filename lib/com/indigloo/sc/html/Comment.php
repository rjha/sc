<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    
    class Comment {

		static function getSummary($commentDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/comment/summary.tmpl' ;
			
			$view->comment = $commentDBRow['description'];
			$view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
			$view->userName = $commentDBRow['user_name'] ;
            $view->loginId = $commentDBRow['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);
			
			$html = Template::render($template,$view);
			
            return $html ;

		}

        static function getWidget($commentDBRow,$options=NULL) {
           
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/comment/text.tmpl' ;
			
            if(is_null($options)) {
                $options = ~UIConstants::COMMENT_ALL ;
            }
			
			$view->id = $commentDBRow['id'];

			$view->title = $commentDBRow['title'];
			$view->postId = $commentDBRow['post_id'];
			$view->itemId = PseudoId::encode($view->postId);

			$view->comment = $commentDBRow['description'];
			$view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
            $view->showUser = false ;

            if($options & UIConstants::COMMENT_USER) {
                $view->loginId = $commentDBRow['login_id'];
                $view->pubUserId = PseudoId::encode($view->loginId);
                $view->userName = $commentDBRow['user_name'] ;
                $view->showUser = true ;
            }

			$encodedId = PseudoId::encode($view->id);
            $params = array('id' => $encodedId, 'q' => urlencode(Url::current()));
            $view->editUrl = Url::createUrl('/qa/comment/edit.php',$params);
            $view->deleteUrl = Url::createUrl('/qa/comment/delete.php',$params);

			$html = Template::render($template,$view);
            return $html ;
        }
        
    }
    
}

?>
