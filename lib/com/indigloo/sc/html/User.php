<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\sc\view\Media as MediaView ;
    use com\indigloo\Util as Util ;
    use com\indigloo\Url as Url ;
    
    class User {
        
        /**
         *
         * @param gSessionLogin $login data stored in session
         * @param userDBRow - DB row for this login_id from sc_denorm_user table
         * 
         */
        static function getProfile($gSessionLogin,$userDBRow) {
			if(is_null($gSessionLogin)) {
				return '' ;
			}

			if(!is_null($gSessionLogin) && ($gSessionLogin->id != $userDBRow['login_id'] )) {
				return '' ;

			}

            $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/user/profile/private.tmpl' ;
			
			$view->name = $userDBRow['name'];
			$view->createdOn = Util::formatDBTime($userDBRow['created_on']);
			$view->email = $userDBRow['email'];
            $view->aboutMe = $userDBRow['about_me'];
            $view->photoUrl = $userDBRow['photo_url'];
            if(empty($view->photoUrl)) {
                $view->photoUrl = '/css/images/twitter-icon.png' ;
            }

            $view->website = $userDBRow['website'];
            $view->blog = $userDBRow['blog'];
            $view->location = $userDBRow['location'];
            $view->nickName = $userDBRow['nick_name'];
            $view->age = $userDBRow['age'];
            $view->aboutMe = $userDBRow['about_me'];
            //@todo
            //$view->gender = $userDBRow['gender'];


            $params = array('q' => urlencode(Url::current()));
            $view->passwordUrl = Url::createUrl("/user/account/change-password.php",$params); 
            $view->editUrl = Url::createUrl("/user/account/edit.php",$params); 
			
			$html = Template::render($template,$view);
            return $html ;

        }

        static function getPhoto($name,$photoUrl) {
            $html = NULL ;
			$template = '/fragments/user/profile/photo.tmpl' ;

			$view = new \stdClass;
            $view->name = $name;
            $view->photoUrl = $photoUrl;
            if(empty($view->photoUrl)) {
                $view->photoUrl = '/css/images/twitter-icon.png' ;
            }

			$html = Template::render($template,$view);
            return $html ;

        }

        static function getPublicInfo($userDBRow) {

            $html = NULL ;
			$view = new \stdClass;
			$template = '/fragments/user/public.tmpl' ;
			
            $view->nickName = $userDBRow['nick_name'];
			$view->name = (empty($view->nickName)) ? $userDBRow['name'] : $view->nickName ;
            $view->aboutMe = $userDBRow['about_me'];
            $view->defaultMe = (empty($view->aboutMe)) ? true : false ;
            $view->photoUrl = $userDBRow['photo_url'];
            if(empty($view->photoUrl)) {
                $view->photoUrl = '/css/images/twitter-icon.png' ;
            }

            $items = array();
            $items['website'] = $userDBRow['website'];
            $items['blog'] = $userDBRow['blog'];
            $items['location'] = $userDBRow['location'];
            $view->items = $items;

			$view->createdOn = Util::formatDBTime($userDBRow['created_on']);

			$html = Template::render($template,$view);
            return $html ;

        }
		
    }
    
}

?>
