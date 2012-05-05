<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;
    use \com\indigloo\sc\util\PseudoId as PseudoId;
    
    class Post {

        static function getGallery($images) {
            if(sizeof($images) == 0 ) { return '' ; }

			$view = new \stdClass;
            $records = array();

            foreach($images as $image) {
                $record = array();
                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? "http://" : "/" ;
                $record['source'] = $prefix.$image->bucket."/".$image->storeName ;
                $record['thumbnail'] = $prefix.$image->bucket."/".$image->thumbnail ;
                $record['title'] = $image->originalName;

  	
				$newxy = Util::foldXY($image->width,$image->height,190,140);
				$record['width'] = $newxy["width"];
				$record['height'] = $newxy["height"];

                $records[] = $record;
            }

            $view->records = $records ;

            $template = '/fragments/post/gallery.tmpl' ;
            $html = Template::render($template,$view);
            return $html;

        }

        static function getLinks($links,$siteDBRow) {
            if(sizeof($links) == 0 ) { return '' ; }

			$view = new \stdClass;
            $view->links = $links;
            $view->hasSite = false;

            if(!empty($siteDBRow)) {
                $view->siteId = $siteDBRow['id'];
                $view->siteUrl = $siteDBRow['canonical_url'];
                $view->hasSite = true ;
            }

            $template = '/fragments/post/link.tmpl' ;
            $html = Template::render($template,$view);
            return $html;
        }

        static function getToolbar($itemId) {
            $view = new \stdClass;
            $view->itemId = $itemId;
            $template = '/fragments/post/toolbar.tmpl' ;
            $html = Template::render($template,$view);
            return $html;
        }

		static function getSmallTile($postDBRow) {

		    $html = NULL ;
			$imagesJson = $postDBRow['images_json'];
			$images = json_decode($imagesJson);
			
			$view = new \stdClass;

			//$view->description = Util::abbreviate($postDBRow['description'],70);
			$view->description = $postDBRow['description'];
			$view->id = $postDBRow['id'];
			$view->itemId = PseudoId::encode($view->id);

			if(sizeof($images) > 0) {
				
				$template = '/fragments/tile/small/image.tmpl' ;
				/* image stuff */
				$image = $images[0] ;
				
				$view->originalName = $image->originalName;
				$view->bucket = $image->bucket;

                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                //@todo remove property exists check after s3 migration?
                $fileName = (property_exists($image,'thumbnail') && !empty($image->thumbnail)) ? $image->thumbnail : $image->storeName ;
				$view->srcImage = $prefix.$image->bucket.'/'.$fileName;
			    	
				$newxy = Util::foldX($image->width,$image->height,100);
				
				$view->width = $newxy["width"];
				$view->height = $newxy["height"];
				
				/* image stuff end */
				$html = Template::render($template,$view);
				
			} else {
				
				$template = '/fragments/tile/small/text.tmpl' ;
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
            $view->loginId = $postDBRow['login_id'];
            $view->pubUserId = PseudoId::encode($view->loginId);

			$template = '/fragments/post/detail.tmpl' ;
			$html = Template::render($template,$view);
			
			return $html ;	
		}

        static function getGroups($postDBRow) {
			$html = NULL ;
			
			$view = new \stdClass;
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

            if(sizeof($groups) == 0 ) {
                return '' ;
            }

            $view->groups = $groups;
			$template = '/fragments/post/group.tmpl' ;
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
