<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
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
                $options = UIConstants::WIDGET_ALL ;
            }
			
			$view->id = $commentDBRow['id'];
			$view->encodedId = PseudoId::encode($view->id);

			$view->title = $commentDBRow['title'];
			$view->postId = $commentDBRow['post_id'];
			$view->itemId = PseudoId::encode($view->postId);

			$view->comment = $commentDBRow['description'];
			$view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
			$view->userName = $commentDBRow['user_name'] ;
			

			$html = Template::render($template,$view);
            return $html ;
        }
        
    }
    
}

?>
