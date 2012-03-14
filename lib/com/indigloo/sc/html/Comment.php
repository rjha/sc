<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    
    class Comment {

		static function getSummary($loginId,$commentDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/comment/summary.tmpl' ;
			
			$view->comment = $commentDBRow['answer'];
			$view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
			$view->userName = $commentDBRow['user_name'] ;
            $view->loginId = $commentDBRow['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);
			
			$html = Template::render($template,$view);
			
            return $html ;

		}

        static function getWidget($gSessionLogin,$commentDBRow) {
           
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/comment/text.tmpl' ;
			
			
			$view->id = $commentDBRow['id'];
			$view->encodedId = PseudoId::encode($view->id);

			$view->title = $commentDBRow['title'];
			$view->postId = $commentDBRow['question_id'];
			$view->itemId = PseudoId::encode($view->postId);

			$view->comment = $commentDBRow['answer'];
			$view->createdOn = Util::formatDBTime($commentDBRow['created_on']);
			$view->userName = $commentDBRow['user_name'] ;
			$view->isLoggedInUser = false ;
		
			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $commentDBRow['login_id'])){
				$view->isLoggedInUser = true ;
			} 

			$html = Template::render($template,$view);
            return $html ;
        }
        
    }
    
}

?>
