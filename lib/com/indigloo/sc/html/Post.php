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


        static function getGalleria($title,$images) {
            if(empty($images) || (sizeof($images) == 0)) { return '' ; }

            $view = new \stdClass;
            $view->title = $title ;
            $records = array();

            foreach($images as $image) {
                $record = array();
                $imgv = self::convertImageJsonObj($image);
                $record["source"] = $imgv["source"];
                $record["thumbnail"] = $imgv["thumbnail"];

                $record["originalName"] = $imgv["name"];
                $record["tname"] = $imgv["tname"];

                //for gallery - both height and width is fixed.
                $td = Util::foldXY($image->width,$image->height,190,140);
                $record["twidth"] = $td["width"];
                $record["theight"] = $td["height"];
                $records[] = $record;
            }

            $view->records = $records ;

            $template = '/fragments/item/galleria.tmpl' ;
            $html = Template::render($template,$view);
            return $html;

        }

        static function getFancybox($title,$images) {
            if(empty($images) || (sizeof($images) == 0)) { return '' ; }

            $view = new \stdClass;
            $view->title = $title ;
            $template = (sizeof($images) == 1 ) ? "fancybox-single.tmpl" : "fancybox.tmpl" ;

            $mainImage = array_shift($images);
            $view->mainImage = self::convertImageJsonObj($mainImage);

            $newd = Util::foldX($mainImage->width,$mainImage->height,550);
            $view->mainImage["width"] = $newd["width"];
            $view->mainImage["height"] = $newd["height"];

            $thumbnails = array();

            foreach($images as $image) {

                $record = array();
                $imgv = self::convertImageJsonObj($image);
                $record["source"] = $imgv["source"];
                $record["thumbnail"] = $imgv["thumbnail"];

                $record["originalName"] = $imgv["name"];
                $record["tname"] = $imgv["tname"];

                //for gallery - both height and width is fixed.
                $td = Util::foldXY($image->width,$image->height,190,140);
                $record["twidth"] = $td["width"];
                $record["theight"] = $td["height"];
                $thumbnails[] = $record;

            }

            $view->thumbnails = $thumbnails ;

            $template = "/fragments/item/".$template ;
            $html = Template::render($template,$view);
            return $html;

        }

        static function getHeader($postView,$loginIdInSession) {

            //toolbar stuff

            $postView->followerId = $loginIdInSession;
            $postView->followingId = $postView->loginId;

            //edit item
            $postView->isLoggedInUser = false ;
            if(!is_null($loginIdInSession) && ($loginIdInSession == $postView->loginId)) {
                $postView->isLoggedInUser = true ;
                $params = array('id' => $postView->itemId , 'q' => urlencode(Url::current()));
                $postView->editUrl = Url::createUrl('/qa/edit.php',$params);
            }

            $template = '/fragments/item/header.tmpl' ;
            $html = Template::render($template,$postView);
            return $html;
        }

        static function getActivity($feedHtml,$commentHtml) {
      
            if(Util::tryEmpty($feedHtml) && Util::tryEmpty($commentHtml)) { 
                return "" ; 
            }

            $view = new \stdClass;
            $view->feedHtml = $feedHtml;
            $view->commentHtml = $commentHtml;
            $template = '/fragments/item/activity.tmpl' ;
            $html = Template::render($template,$view);

            return $html;
        }

        static function getLinks($links,$siteDBRow) {
            if(sizeof($links) == 0 ) { return '' ; }

            $view = new \stdClass;
            $view->links = $links;
            $view->hasSite = false;

            if(!empty($siteDBRow)) {
                $view->siteId = $siteDBRow["id"];
                $view->siteUrl = $siteDBRow["canonical_url"];
                $view->hasSite = true ;
            }

            $template = '/fragments/item/links.tmpl' ;
            $html = Template::render($template,$view);
            return $html;
        }

        static function getToolbar($loginIdInSession,$postLoginId, $itemId) {
            $view = new \stdClass;
            $view->itemId = $itemId;
            $view->followerId = $loginIdInSession;
            $view->followingId = $postLoginId;

            //edit item
            $view->isLoggedInUser = false ;
            if(!is_null($loginIdInSession) && ($loginIdInSession == $postLoginId)) {
                $view->isLoggedInUser = true ;
                $params = array('id' => $itemId , 'q' => urlencode(Url::current()));
                $view->editUrl = Url::createUrl('/qa/edit.php',$params);
            }

            $template = '/fragments/item/toolbar.tmpl' ;
            $html = Template::render($template,$view);
            return $html;
        }

        static function getSmallTile($postDBRow) {
            $html = NULL ;
            $template = NULL ;
            $voptions = array("abbreviate" => true);
            $view = self::createPostView($postDBRow, $voptions);

            if($view->hasImage) {
                //Add thumbnail width and height
                $td = Util::foldX($view->width,$view->height,100);
                $view->twidth = $td["width"];
                $view->theight = $td["height"];
                $template = '/fragments/tile/small/image.tmpl' ;

            } else {
                $template = '/fragments/tile/small/text.tmpl' ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getTile($postDBRow,$options=NULL) {

            $html = NULL ;
            $template = NULL ;

            if(is_null($options)) {
                $options = UIConstants::TILE_ALL & ~UIConstants::TILE_REMOVE ;
            }

            $voptions = array("abbreviate" => true ,"group" => true);
            $view = self::createPostView($postDBRow,$voptions);

            if($view->hasImage) {
                $template = '/fragments/tile/image.tmpl' ;
                //Add thumbnail width and height
                $td = Util::foldX($view->width,$view->height,190);
                $view->twidth = $td["width"];
                $view->theight = $td["height"];

            } else {
                $template = '/fragments/tile/text.tmpl' ;
            }


            //set action flags
            $view->hasLike = $options & UIConstants::TILE_LIKE ;
            $view->hasSave = $options & UIConstants::TILE_SAVE ;
            $view->hasRemove = $options & UIConstants::TILE_REMOVE  ;
            $view->hasMore = $options & UIConstants::TILE_MORE ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getDetail($postView,$links) {

            $postView->links = $links ;

            $html = NULL ;
            $template = '/fragments/item/detail.tmpl' ;
            $html = Template::render($template,$postView);
            return $html ;
        }

        static function getGroups($postView) {
            $html = NULL ;

            if(!$postView->hasGroups) return '' ;
            $template = '/fragments/item/group.tmpl' ;
            $html = Template::render($template,$postView);
            return $html ;
        }

        static function getUserPanel($postView) {
            $html = NULL ;
            $template = '/fragments/item/user-panel.tmpl' ;
            $html = Template::render($template,$postView);
            return $html ;
        }

        static function getSiteMeta($siteMetaRow) {
            if(empty($siteMetaRow)) {
                //no site information available
                return "" ;
            }

            $html = NULL ;
            $template = '/fragments/item/site-meta.tmpl' ;
            
            $view = new \stdClass;
            $view->siteId = $siteMetaRow["id"];
            $view->siteUrl = $siteMetaRow["canonical_url"];

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getSitePanel($sitePostRows) {

            if(sizeof($sitePostRows) == 0 ) {
                return "" ;
            }

            $html = NULL ;
            $template = '/fragments/item/site-panel.tmpl' ;
            $view = new \stdClass;

            $posts = array();
            foreach($sitePostRows as $row) {
                $postView = self::createPostView($row);
                if($postView->hasImage) {
                    array_push($posts,$postView);
                }
            }

            $view->posts = $posts ;
            $html = Template::render($template,$view);
            return $html ;

        }
        
        static function getWidget($postDBRow,$options=NULL) {

            $html = NULL ;
            $voptions = array("abbreviate" => true);
            $view = self::createPostView($postDBRow,$voptions);

            if($view->hasImage) {
                $template = '/fragments/widget/image.tmpl' ;
                //Add thumbnail width and height
                $td = Util::foldX($view->width,$view->height,100);
                $view->twidth = $td["width"];
                $view->theight = $td["height"];

            } else {
                $template = '/fragments/widget/text.tmpl' ;
            }


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
            $voptions = array("abbreviate" => true);
            $view = self::createPostView($postDBRow,$voptions);
            if(is_null($options)) {
                $options = UIConstants::WIDGET_ALL ;
            }

             if($view->hasImage) {
                $template = '/fragments/widget/admin/image.tmpl' ;
                //Add thumbnail width and height
                $td = Util::foldX($view->width,$view->height,100);
                $view->twidth = $td["width"];
                $view->theight = $td["height"];

            } else {
                $template = '/fragments/widget/admin/text.tmpl' ;
            }

            $params = array('id' => $view->itemId, 'q' => Url::current());
            $view->editUrl = Url::createUrl('/qa/edit.php',$params);
            $view->deleteUrl = Url::createUrl('/qa/delete.php',$params);
            
            $view->feature = ($postDBRow['fp_bit'] == 0 ) ? true : false ;
            $view->unfeature = ($postDBRow['fp_bit'] == 1 ) ? true : false ;

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getBookmarkWidget($postDBRow) {

            $html = NULL ;
            $voptions = array("abbreviate" => true);
            $view = self::createPostView($postDBRow,$voptions);

             if($view->hasImage) {
                $template = '/fragments/widget/bookmark/image.tmpl' ;
                //Add thumbnail width and height
                $td = Util::foldX($view->width,$view->height,100);
                $view->twidth = $td["width"];
                $view->theight = $td["height"];

            } else {
                $template = '/fragments/widget/bookmark/text.tmpl' ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function createPostView($row,$voptions=NULL) {

            $voptions = empty($voptions) ? array() : $voptions ;

            //default options
            $options = array();
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
            $view->images = NULL ;
            
            $view->hasGroups = false ;
            $view->groups= array();
            $view->id = $row['id'];
            $view->itemId = PseudoId::encode($view->id);

            // title in DB is 128 chars long.
            // here on page we want to use a 70 char title.
            // also used in item images alt text
            // clean up bad utf-8 data for display
            $view->title = Util::filterBadUtf8($row['title']) ;
            $view->title = Util::abbreviate($view->title,70);

            $view->description = Util::filterBadUtf8($row['description']) ;
            if($options["abbreviate"]) {
                $view->description = Util::abbreviate($view->description,160);
            }
            

            $view->userName = $row['user_name'];
            $view->createdOn = Util::formatDBTime($row['created_on'], AppConstants::TIME_MDYHM);
            $view->pubUserId = PseudoId::encode($row['login_id']);
            $view->loginId = $row['login_id'];
            $view->userPageURI = "/pub/user/".$view->pubUserId;
           

            //process post image.
            if( (!empty($images)) && (sizeof($images) > 0) && $options["image"]) {

                /* process image #1 */
                $view->hasImage = true ;
                $image = $images[0] ;
                $imgv = self::convertImageJsonObj($image);
                $view->thumbnail = $imgv["thumbnail"];
                $view->height = $imgv["height"];
                $view->width = $imgv["width"];
                $view->srcImage = $imgv["source"];
                /* assign all images */
                $view->images = $images ;
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
                        //@imp @todo @hack
                        // dirty hack - for single quotes in group name - for old data
                        // anything indexed as flury&#039;s - should be converted to flury
                        // now we ignore the single quote in group name so we should be fine
                        $slug = str_replace("&#039;s","",$slug);
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

        static function getTileImage($json) {
            $images = json_decode($json);
            $imgv = array();

            if( (!empty($images)) && (sizeof($images) > 0)) {
                //work with image #1
                $image = $images[0] ;
                $imgv = self::convertImageJsonObj($image);
                $td = Util::foldXY($image->width,$image->height,40,40);
                $imgv["twidth"] = $td["width"];
                $imgv["theight"] = $td["height"];

            } else {
                $imgv["name"] = "placeholder" ;
                $imgv["tname"] = "placeholder" ;
                $imgv["source"] = "/css/asset/sc/twitter-icon.png" ;
                $imgv["thumbnail"] = "/css/asset/sc/twitter-icon.png" ;
                $imgv["width"] = 48;
                $imgv["height"] = 48;
                $imgv["twidth"] = 40;
                $imgv["theight"] = 40;
            }

            return $imgv ;
        }

        static function convertImageJsonObj($jsonObj) {
            $view = array();

            if((strcmp($jsonObj->store,"s3") == 0 ) || (strcmp($jsonObj->store,"local") == 0)) {
                $view["name"] = $jsonObj->originalName ;
                $prefix = ($jsonObj->store == 's3') ? 'http://' : Url::base().'/' ;
                $fileName = NULL ;

                //@imp: if thumbnail is not available then fallback on original image
                if(property_exists($jsonObj,"thumbnailName")) {
                    $view["tname"] = $jsonObj->thumbnailName ;
                    $fileName = $jsonObj->thumbnail ;

                } else {
                    $view["tname"] = $jsonObj->originalName ;
                    $fileName = $jsonObj->storeName ;
                }

                $view["source"] = $prefix.$jsonObj->bucket.'/'.$jsonObj->storeName;
                $view["thumbnail"] = $prefix.$jsonObj->bucket.'/'.$fileName ;
                $view["width"] = $jsonObj->width ;
                $view["height"] = $jsonObj->height;
                //@todo add thumbnail width and height to image json data

            } else {

                $message = sprintf("Unknown image store %s ", $jsonObj->store);
                trigger_error($message,E_USER_ERROR);
            }

            return $view ;

        }

    }

}

?>
