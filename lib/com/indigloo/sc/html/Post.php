<?php

namespace com\indigloo\sc\html {

    use \com\indigloo\Template as Template;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\util\StringUtil as StringUtil ;

    use \com\indigloo\sc\util\PseudoId as PseudoId;
    use \com\indigloo\sc\ui\Constants as UIConstants ;
    use \com\indigloo\sc\Constants as AppConstants ;

    class Post {

        static function getGallery($images) {
            if(sizeof($images) == 0 ) { return '' ; }

            $view = new \stdClass;
            $records = array();

            foreach($images as $image) {
                $record = array();

                //show thumbnail + original image
                //@imp: if thumbnail is not available then fallback on original image
                $tname = (property_exists($image,'thumbnailName')) ? $image->thumbnailName : $image->originalName ;

                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                $tfile = (property_exists($image,'thumbnail')) ? $image->thumbnail : $image->storeName ;
                $timage = $prefix.$image->bucket.'/'.$tfile;

                $record['source'] = $prefix.$image->bucket.'/'.$image->storeName ;
                $record['thumbnail'] = $timage;
                $record['title'] = $image->originalName;
                $record['tname'] = $tname;

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

        static function getToolbar($itemId,$loginId,$postLoginId) {
            $view = new \stdClass;
            $view->itemId = $itemId;
            $view->followerId = $loginId;
            $view->followingId = $postLoginId;

            //edit item
            $view->isLoggedInUser = false ;
            if(!is_null($loginId) && ($loginId == $postLoginId)) {
                $view->isLoggedInUser = true ;
                $params = array('id' => $itemId , 'q' => urlencode(Url::current()));
                $view->editUrl = Url::createUrl('/qa/edit.php',$params);
            }

            $template = '/fragments/post/toolbar.tmpl' ;
            $html = Template::render($template,$view);
            return $html;
        }

        static function getSmallTile($postDBRow) {
            $html = NULL ;
            $voptions = array("abbreviate" => true , "imageWidth" => 100 );
            $view = self::createPostView($postDBRow, $voptions);

            $template = ($view->hasImage) ?
                '/fragments/tile/small/image.tmpl' : '/fragments/tile/small/text.tmpl' ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getTile($postDBRow,$options=NULL) {

            $html = NULL ;
            if(is_null($options)) {
                $options = UIConstants::TILE_ALL & ~UIConstants::TILE_REMOVE ;
            }

            $voptions = array("abbreviate" => true ,"group" => true);
            $view = self::createPostView($postDBRow,$voptions);
            $template = ($view->hasImage) ?
                '/fragments/tile/image.tmpl' : '/fragments/tile/text.tmpl' ;

            //set action flags
            $view->hasLike = $options & UIConstants::TILE_LIKE ;
            $view->hasSave = $options & UIConstants::TILE_SAVE ;
            $view->hasRemove = $options & UIConstants::TILE_REMOVE  ;
            $view->hasMore = $options & UIConstants::TILE_MORE ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getDetail($postDBRow) {
            $html = NULL ;
            $voptions = array("image" => false);
            $view = self::createPostView($postDBRow,$voptions) ;
            $template = '/fragments/post/detail.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getGroups($postDBRow) {
            $html = NULL ;
            $voptions = array("image" => false, "group" => true);
            $view = self::createPostView($postDBRow,$voptions) ;

            if(!$view->hasGroups) return '' ;
            $template = '/fragments/post/group.tmpl' ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getWidget($postDBRow,$options=NULL) {

            $html = NULL ;
            $view = self::createPostView($postDBRow,NULL);

            $template = ($view->hasImage) ? '/fragments/widget/image.tmpl' : '/fragments/widget/text.tmpl' ;
            if(is_null($options)) {
                $options = UIConstants::WIDGET_EDIT | UIConstants::WIDGET_DELETE ;
            }

            $view->hasEdit = $options & UIConstants::WIDGET_EDIT ;
            $view->hasDelete = $options & UIConstants::WIDGET_DELETE ;
            $params = array('id' => $view->itemId, 'q' => urlencode(Url::current()));
            $view->editUrl = Url::createUrl('/qa/edit.php',$params);
            $view->deleteUrl = Url::createUrl('/qa/delete.php',$params);

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getAdminWidget($postDBRow,$options=NULL) {

            $html = NULL ;
            $view = self::createPostView($postDBRow,NULL);
            if(is_null($options)) {
                $options = UIConstants::WIDGET_ALL ;
            }

            $template = ($view->hasImage) ? '/fragments/widget/admin/image.tmpl' : '/fragments/widget/admin/text.tmpl' ;
            $params = array('id' => $view->itemId, 'q' => Url::current());
            $view->editUrl = Url::createUrl('/qa/edit.php',$params);
            $view->deleteUrl = Url::createUrl('/qa/delete.php',$params);
            $view->feature = ($postDBRow['is_feature'] == 0 ) ? true : false ;
            $view->unfeature = ($postDBRow['is_feature'] == 1 ) ? true : false ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function createPostView($row,$voptions=NULL) {

            $voptions = empty($voptions) ? array() : $voptions ;

            //default options
            $options = array();
            $options["imageWidth"] = 190 ;
            $options["abbreviate"] = false ;
            $options["image"] = true ;
            $options["group"] = false ;


            //override defaults
            foreach($voptions as $key => $value) {
                $options[$key] = $value ;
            }

            $imagesJson = $row["images_json"];
            $images = json_decode($imagesJson);

            $view = new \stdClass;

            $view->hasImage = false ;
            $view->hasGroups = false ;
            $view->id = $row['id'];
            $view->itemId = PseudoId::encode($view->id);
            $view->description = ($options["abbreviate"]) ?
                    Util::abbreviate($row["description"],160) : $row['description'] ;

            $view->userName = $row['user_name'];
            $view->createdOn = Util::formatDBTime($row['created_on'], AppConstants::TIME_MDYHM);
            $view->pubUserId = PseudoId::encode($row['login_id']);
            $view->userPageURI = "/pub/user/".$view->pubUserId;

            //process post image.
            if( (!empty($images)) && (sizeof($images) > 0) && $options["image"]) {
                /* image stuff */
                $view->hasImage = true ;
                $image = $images[0] ;
                $view->bucket = $image->bucket;

                $view->originalName =
                    (property_exists($image,'thumbnailName')) ?  $image->thumbnailName : $image->originalName ;
                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : '/' ;
                $fileName = (property_exists($image,'thumbnail')) ? $image->thumbnail : $image->storeName ;

                $view->srcImage = $prefix.$image->bucket.'/'.$fileName;
                $newxy = Util::foldX($image->width,$image->height,$options["imageWidth"]);
                $view->width = $newxy["width"];
                $view->height = $newxy["height"];
            }

            //process groups
            if($options["group"] === true) {
                $group_slug = $row['group_slug'];
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
                }
            }

            return $view ;
        }

    }

}

?>
