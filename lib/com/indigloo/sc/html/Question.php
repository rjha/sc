<?php

namespace com\indigloo\sc\html {

    use com\indigloo\Template as Template;
    use com\indigloo\Util as Util ;
    use com\indigloo\Constants as Constants ;
    use com\indigloo\util\StringUtil as StringUtil ;
    
    class Question {

		static function getSimpleTile($questionDBRow) {

		    $html = NULL ;
			$imagesJson = $questionDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;
			$view->description = Util::abbreviate($questionDBRow['description'],70);
			$view->userPageURI = "/pub/user/".$questionDBRow['login_id'];
			$view->id = $questionDBRow['id'];

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

		static function getTile($questionDBRow) {

		    $html = NULL ;
			$imagesJson = $questionDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;
			$view->description = Util::abbreviate($questionDBRow['description'],160);
			$view->userPageURI = "/pub/user/".$questionDBRow['login_id'];
			$view->id = $questionDBRow['id'];
				
			$view->userName = $questionDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($questionDBRow['created_on']);
			$view->tags = $questionDBRow['tags'];

            $group_slug = $questionDBRow['group_slug'];
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

		static function getDetail($questionDBRow) {
			$html = NULL ;
			
			$view = new \stdClass;
			$view->description = $questionDBRow['description'];
			$view->id = $questionDBRow['id'];
			
				
			$view->userName = $questionDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($questionDBRow['created_on']);
			$view->tags = $questionDBRow['tags'];
            $view->loginId = $questionDBRow['login_id'];

			
			$template = '/fragments/question/detail.tmpl' ;
			$html = Template::render($template,$view);
			
			return $html ;	
		}

        static function getEditBar($gSessionLogin,$questionDBRow){
			$html = NULL ;
			$template = '/fragments/question/edit-bar.tmpl' ;

			$view = new \stdClass;
            $view->isLoggedInUser = false ;
            $view->id = $questionDBRow['id'];

			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $questionDBRow['login_id'])){
				$view->isLoggedInUser = true ;
            } 

			$html = Template::render($template,$view);
            return $html ;

        }

		static function getWidget($gSessionLogin,$questionDBRow) {
           
			$html = NULL ;

			$template = '/fragments/widget/text.tmpl' ;
			$imagesJson = $questionDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;
			$view->description = $questionDBRow['description'];
			$view->id = $questionDBRow['id'];
			
				
			$view->userName = $questionDBRow['user_name'];
			$view->createdOn = Util::formatDBTime($questionDBRow['created_on']);
			$view->tags = $questionDBRow['tags'];

			$view->isLoggedInUser = false ;

			if(!is_null($gSessionLogin) && ($gSessionLogin->id == $questionDBRow['login_id'])){
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
