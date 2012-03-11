<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    
    class User {
        
		static function get3mikProfile($loginId,$userDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/user/profile/3mik.tmpl' ;
			
			$view->name = $userDBRow['first_name']. ' '.$userDBRow['last_name'];
			$view->createdOn = Util::formatDBTime($userDBRow['created_on']);
			$view->email = $userDBRow['email'];
			
			$html = Template::render($template,$view);
            return $html ;
		}
		
		static function getTwitterProfile($loginId,$userDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/user/profile/twitter.tmpl' ;
			
			$view->name = $userDBRow['name'];
			$view->createdOn = Util::formatDBTime($userDBRow['created_on']);
			$view->location = $userDBRow['location'];
			$view->image = $userDBRow['profile_image'];

			if(empty($view->image)) {
				$view->image = "/nuke/twitter-bird.png" ;
			}

			$html = Template::render($template,$view);
            return $html ;
		}

		static function getFacebookProfile($loginId,$userDBRow) {
		    $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/user/profile/facebook.tmpl' ;
			
			$view->name = $userDBRow['name'];
			$view->createdOn = Util::formatDBTime($userDBRow['created_on']);
			$view->email = $userDBRow['email'];
				
			$html = Template::render($template,$view);
            return $html ;
		}

        static function getProfile($gSessionLogin,$userDBRow) {
			if(is_null($gSessionLogin)) {
				return '' ;
			}

			if(!is_null($gSessionLogin) && ($gSessionLogin->id != $userDBRow['login_id'] )) {
				return '' ;

			}

			$loginId = $gSessionLogin->id ;

			$html = NULL ;
			$provider = $userDBRow['provider'];

			switch($provider) {
				case \com\indigloo\sc\auth\Login::MIK :
					$html = self::get3mikProfile($loginId,$userDBRow);
					break;
				case \com\indigloo\sc\auth\Login::FACEBOOK :
					$html = self::getFacebookProfile($loginId,$userDBRow);
					break;
				case \com\indigloo\sc\auth\Login::TWITTER :
					$html = self::getTwitterProfile($loginId,$userDBRow);
					break;
				default:
					trigger_error("Unknown user provider",E_USER_ERROR);
			}

			return $html ;
        }
		
    }
    
}

?>
