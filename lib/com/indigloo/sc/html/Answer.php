<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    
    class Answer {

		static function getSummary($loginId,$answerDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/answer/summary.tmpl' ;
			
			$view->answer = $answerDBRow['answer'];
			$view->createdOn = Util::formatDBTime($answerDBRow['created_on']);
			$view->userName = $answerDBRow['user_name'] ;
            $view->loginId = $answerDBRow['login_id'];
			
			$html = Template::render($template,$view);
			
            return $html ;

		}

        static function getWidget($gSessionLogin,$answerDBRow) {
           
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/answer/text.tmpl' ;
			
			
			$view->id = $answerDBRow['id'];
			$view->title = $answerDBRow['title'];
			$view->questionId = $answerDBRow['question_id'];
			$view->answer = $answerDBRow['answer'];
			$view->createdOn = Util::formatDBTime($answerDBRow['created_on']);
			$view->userName = $answerDBRow['user_name'] ;
			$view->isLoggedInUser = false ;
		
			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $answerDBRow['login_id'])){
				$view->isLoggedInUser = true ;
			} 

			$html = Template::render($template,$view);
            return $html ;
        }
        
    }
    
}

?>
