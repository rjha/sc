<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Message as FormMessage;
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

            $imagesJson = $postDBRow['images_json'];
            $images = json_decode($imagesJson);

            $linksJson = $postDBRow['links_json'];
            $links = json_decode($linksJson);

            /* data for facebook share link */
            $itemObj = new \stdClass ;
            $itemObj->appId = Config::getInstance()->get_value("facebook.app.id");
            $itemObj->host = "http://" .$_SERVER["HTTP_HOST"] ;
            /* google+ cannot redirect to local box */
            $itemObj->netHost = "http://www.3mik.com" ;
            $itemObj->callback = $itemObj->host."/callback/fb-share.php" ;


            if(sizeof($images) > 0 ) {
                $image = $images[0];
                //Facebook dialog needs absolute URL
                $prefix = (property_exists($image,'store') && ($image->store == 's3')) ? 'http://' : $itemObj->host."/";
                /* use original image for og snippets, smaller images may be ignored */
                $itemObj->picture = $prefix.$image->bucket."/".$image->storeName ;
            } else {
                 $itemObj->picture = $itemObj->host."/css/images/logo.png";
            }

            //do not urlencode - as we use this value as canonical url
            $itemObj->link = $itemObj->host."/item/".$itemId ;
            $itemObj->netLink = $itemObj->netHost."/item/".$itemId ;
            $itemObj->name = $postDBRow['title'];
            $itemObj->description = $postDBRow['description'] ;
            $strItemObj = json_encode($itemObj);
            //make the json string form safe
            $strItemObj = Util::formSafeJson($strItemObj);


            $commentDao = new \com\indigloo\sc\dao\Comment();
            $commentDBRows = $commentDao->getOnPostId($postId);

            $gWeb = \com\indigloo\core\Web::getInstance();
            $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
            $loginId = \com\indigloo\sc\auth\Login::tryLoginIdInSession();

            $xids = array();
            $xrows = array();
            $group_slug = $postDBRow['group_slug'];
            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names = $groupDao->slugToName($postDBRow['group_slug']);

            if(!Util::tryEmpty($group_slug)) {

                $groups = explode(Constants::SPACE,$group_slug);
                $sphinx = new \com\indigloo\sc\search\SphinxQL();

                foreach($groups as $group) {
                    $ids = $sphinx->getGroups($group,0,8);
                    foreach($ids as $id){
                        if(!in_array($id,$xids) && ($id != $postId)) {
                            array_push($xids,$id);
                            if(sizeof($xids) >= 8 ) { break; }
                        }
                    }
                    if(sizeof($xids) >= 8 ) { break; }
                }

                //get posts on groups
                if(!empty($xids)) {
                    $xrows = $postDao->getOnSearchIds($xids);
                }
            }

            $catCode = $postDBRow['cat_code'];

            if(!Util::tryEmpty($catCode)) {
                $categoryDao = new \com\indigloo\sc\dao\Category();
                $catRows = $categoryDao->getLatest($catCode,4);
                foreach($catRows as $catRow) {
                    if(!in_array($catRow['id'],$xids) && ($catRow['id'] != $postId)) {
                        array_push($xrows,$catRow);
                    }
                }
            }

            if(sizeof($xrows) < 16 ) {
                //how many?
                $limit = 16 - (sizeof($xrows)) ;
                $randomRows = $postDao->getRandom($limit);
                $xrows = array_merge($xrows,$randomRows);
            }

            $siteDao = new \com\indigloo\sc\dao\Site();
            $siteDBRow = $siteDao->getOnPostId($postId);

            $loginUrl = "/user/login.php?q=".$_SERVER['REQUEST_URI'];
            $formErrors = FormMessage::render();


            $pageTitle = Util::abbreviate($postDBRow['title'],70);
            $metaDescription = Util::abbreviate($postDBRow['description'],160);
            $metaKeywords = SeoData::getMetaKeywords($group_names);
            $pageUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

            $file = APP_WEB_DIR. '/view/item.php' ;
            include($file);
        }
    }
}
?>
