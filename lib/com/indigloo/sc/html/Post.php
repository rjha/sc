<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    
    class Post {

		static function getSimpleTile($postDBRow) {

		    $html = NULL ;
			$imagesJson = $postDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;

			$view->description = Util::abbreviate($postDBRow['description'],70);
			$view->id = $postDBRow['id'];
			$view->itemId = PseudoId::encode($view->id);

			if(sizeof($images) > 0) {
				
				$template = '/fragments/tile/simple/image.tmpl' ;
				/* image stuff */
				$image = $images[0] ;
				
				$view->originalName = $image->originalName;
				$view->bucket = $image->bucket;

                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                //@todo remove property exists check after s3 migration?
                $fileName = (property_exists($image,'thumbnail') && !empty($image->thumbnail)) ? $image->thumbnail : $image->storeName ;
				$view->srcImage = $prefix.$image->bucket.'/'.$fileName;
			    	
				$newxy = Util::foldX($image->width,$image->height,190);
				
				$view->width = $newxy["width"];
				$view->height = $newxy["height"];
				
				/* image stuff end */
				$html = Template::render($template,$view);
				
			} else {
				
				$template = '/fragments/tile/simple/text.tmpl' ;
				$html = Template::render($template,$view);
			}
			
            return $html ;
			
        }

		static function getTile($postDBRow) {

		    $html = NULL ;
			$imagesJson = $postDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;
			$view->description = Util::abbreviate($postDBRow['description'],160);

			$view->userPageURI = "/pub/user/".PseudoId::encode($postDBRow['login_id']);
			$view->id = $postDBRow['id'];
			$view->itemId = PseudoId::encode($view->id);
				
			$view->userName = $postDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($postDBRow['created_on']);
			$view->tags = $postDBRow['tags'];

            $group_slug = $postDBRow['group_slug'];
            $groups = array();

            if(!is_null($group_slug) && (strlen($group_slug) > 0)) {
                $slugs = explode(Constants::SPACE,$group_slug);
                $display = NULL ;

                foreach($slugs as $slug) {
                    if(empty($slug)) continue ;

                    $display = StringUtil::convertKeyToName($slug);
                    $groups[] = array("slug" => $slug, "display"=> $display);
                }
            }

            if(sizeof($groups) > 0 ) {
                $view->hasGroups = true ;
                $view->groups = $groups;
            }else {
                $view->hasGroups = false ;
            }
		    	
			if(sizeof($images) > 0) {
				
				$template = '/fragments/tile/image.tmpl' ;
				/* image stuff */
				$image = $images[0] ;
				
				$view->originalName = $image->originalName;
				$view->bucket = $image->bucket;

                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                //@todo remove property exists check after s3 migration?
                $fileName = (property_exists($image,'thumbnail') && !empty($image->thumbnail)) ? $image->thumbnail : $image->storeName ;
				$view->srcImage = $prefix.$image->bucket.'/'.$fileName;
			    	
				$newxy = Util::foldX($image->width,$image->height,190);
				
				$view->width = $newxy["width"];
				$view->height = $newxy["height"];
				
				/* image stuff end */
				$html = Template::render($template,$view);
				
			} else {
				
				$template = '/fragments/tile/text.tmpl' ;
				$html = Template::render($template,$view);
			}
			
            return $html ;
			
        }

		static function getDetail($postDBRow) {
			$html = NULL ;
			
			$view = new \stdClass;
			$view->description = $postDBRow['description'];
			
				
			$view->userName = $postDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($postDBRow['created_on']);
			$view->tags = $postDBRow['tags'];
            $view->loginId = $postDBRow['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);

            $group_slug = $postDBRow['group_slug'];
            $groups = array();

            if(!is_null($group_slug) && (strlen($group_slug) > 0)) {
                $slugs = explode(Constants::SPACE,$group_slug);
                $display = NULL ;

                foreach($slugs as $slug) {
                    if(empty($slug)) continue ;

                    $display = StringUtil::convertKeyToName($slug);
                    $groups[] = array("slug" => $slug, "display"=> $display);
                }
            }

            if(sizeof($groups) > 0 ) {
                $view->hasGroups = true ;
                $view->groups = $groups;
            }else {
                $view->hasGroups = false ;
            }

			$template = '/fragments/post/detail.tmpl' ;
			$html = Template::render($template,$view);
			
			return $html ;	
		}

        static function getEditBar($gSessionLogin,$postDBRow){
			$html = NULL ;
			$template = '/fragments/post/edit-bar.tmpl' ;

			$view = new \stdClass;
            $view->isLoggedInUser = false ;
            $view->id = $postDBRow['id'];
            $view->itemId = PseudoId::encode($view->id);

			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $postDBRow['login_id'])){
				$view->isLoggedInUser = true ;
            } 

			$html = Template::render($template,$view);
            return $html ;

        }

		static function getWidget($gSessionLogin,$postDBRow) {
           
			$html = NULL ;

			$template = '/fragments/widget/text.tmpl' ;
			$imagesJson = $postDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;
			$view->description = $postDBRow['description'];
			$view->id = $postDBRow['id'];
			$view->itemId = PseudoId::encode($view->id);
			
				
			$view->userName = $postDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($postDBRow['created_on']);
			$view->tags = $postDBRow['tags'];

			$view->isLoggedInUser = false ;

			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $postDBRow['login_id'])){
				$view->isLoggedInUser = true ;
			} 
			
			if(!empty($images) && (sizeof($images) > 0)) {
				
				/* image stuff */
				$template = '/fragments/widget/image.tmpl' ;
				
				$image = $images[0] ;
				
				$view->originalName = $image->originalName;
				$view->bucket = $image->bucket;

                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                $fileName = (property_exists($image,'thumbnail') && !empty($image->thumbnail)) ? $image->thumbnail : $image->storeName ;
				$view->srcImage = $prefix.$image->bucket.'/'.$fileName;
				
				$newxy = Util::foldX($image->width,$image->height,190);
				
				$view->width = $newxy["width"];
				$view->height = $newxy["height"];
				
				/* image stuff end */
				
				
			}
			
			$html = Template::render($template,$view);
            return $html ;
			
        }

        
    }
    
}

?>
