<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\sc\html\Seo as SeoData ;

    class Post {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $itemId = Util::getArrayKey($params,"item_id");

            if($itemId < 1200) {
                //@todo remove permanent redirect
                $redirectUrl = "/item/".PseudoId::encode($itemId) ;
                header( "HTTP/1.1 301 Moved Permanently" );
                header( "Location: ".$redirectUrl );
                exit ;
            }

            $postDao = new \com\indigloo\sc\dao\Post();
            $postId = PseudoId::decode($itemId);
            $postDBRow = $postDao->getOnId($postId);

            if(empty($postDBRow)) {
                //not found
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            $options = array();
            $options["group"] = true ;
            $postView = \com\indigloo\sc\html\Post::createPostView($postDBRow,$options);

            // links is separate from postView for historical reasons 
            $linksJson = $postDBRow['links_json'];
            $dblinks = json_decode($linksJson);

            $links = array();
            foreach($dblinks as $link) {
                $link = Url::addHttp($link);
                array_push($links,$link);
            }

            /* data for facebook/google+ dialogs */
            $itemObj = new \stdClass ;
            $itemObj->appId = Config::getInstance()->get_value("facebook.app.id");
            $itemObj->host = Url::base();

            /* google+ cannot redirect to local box */
            $itemObj->netHost = "http://www.3mik.com" ;
            $itemObj->callback = $itemObj->host."/callback/fb-share.php" ;

            if($postView->hasImage) {
                /* use original image for og snippets, smaller images may be ignored */
                /* facebook and google+ dialogs need absolute URL */
                $itemObj->picture = $postView->srcImage ;
            } else {
                $itemObj->picture = $itemObj->host."/css/asset/sc/logo.png";
            }

            //do not urlencode - as we use this value as canonical url
            $itemObj->link = $itemObj->host."/item/".$itemId ;
            $itemObj->netLink = $itemObj->netHost."/item/".$itemId ;

            // title in DB is 128 chars long.
            // here on page we want to use a 70 char title.
            // also used in item images alt text
            // item description should be 160 chars.
            $itemObj->title = Util::abbreviate($postView->title,70);
            $itemObj->title = sprintf("item %s - %s",$itemId,$itemObj->title);

            $itemObj->description = Util::abbreviate($postView->description,160);
            $itemObj->description = sprintf("item %s - %s by user %s",
                $itemId,$itemObj->description,$postView->userName) ;
           

            $strItemObj = json_encode($itemObj);
            //make the item json string form safe
            $strItemObj = Util::formSafeJson($strItemObj);

            /* likes data */
            $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();
            $likeDBRows = $bookmarkDao->getLikeOnItemId($itemId);
            
            $gWeb = \com\indigloo\core\Web::getInstance();
            /* sticky is used by comment form */
            $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
            $gRegistrationPopup = false ;

            $loginIdInSession = \com\indigloo\sc\auth\Login::tryLoginIdInSession();
            
            //show registration popup
            if(is_null($loginIdInSession)) {
                $register_popup =  $gWeb->find("sc:browser:registration:popup");
                $register_popup = (is_null($register_popup)) ? false : $register_popup ;
                
                if(!$register_popup) {
                    $gRegistrationPopup = true ;
                    $gWeb->store("sc:browser:registration:popup", true);
                }
                
            }
            
            $group_slug = $postDBRow["group_slug"];
            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names = $groupDao->tokenizeSlug($group_slug,",",true);


            $pageTitle = $itemObj->title;
            $metaKeywords = SeoData::getMetaKeywords($group_names);
            $pageUrl = Url::base().Url::current() ;
             
            $file = APP_WEB_DIR. '/view/item.php' ;
            include($file);
        }
    }
}
?>
