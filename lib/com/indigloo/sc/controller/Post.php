<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
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
            $itemObj->description = Util::abbreviate($postView->description,160);

            $strItemObj = json_encode($itemObj);
            //make the item json string form safe
            $strItemObj = Util::formSafeJson($strItemObj);

            /* comments data */
            $commentDao = new \com\indigloo\sc\dao\Comment();
            $commentDBRows = $commentDao->getOnPostId($postId);

            $gWeb = \com\indigloo\core\Web::getInstance();
            $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
            $loginIdInSession = \com\indigloo\sc\auth\Login::tryLoginIdInSession();

            $xids = array();
            $xrows = array();
            $limit = 10 ;

            $group_slug = $postDBRow["group_slug"];
            $groupDao = new \com\indigloo\sc\dao\Group();
            $group_names = $groupDao->slugToName($postDBRow["group_slug"]);
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            
            /* 
             * 
             * when fetching xrows or post related rows, tags(groups) have 
             * highest priority. Here we assume that we should be able to bring
             * in 16 items using 2 tags.
             * 
             * next we use sphinx quorum operator on title + groups/#of hits
             * 
             * if above fails then just bring in more rows from post.category
             *
             * 
             */

            if(!Util::tryEmpty($group_slug)) {

                $groups = explode(Constants::SPACE,$group_slug);
 
                foreach($groups as $group) {

                    $ids = $sphinx->getPostByGroup($group,0,12);
                    foreach($ids as $id){
                        if(!in_array($id,$xids) && ($id != $postId)) {
                            array_push($xids,$id);
                            if(sizeof($xids) >= 12 ) { break; }
                        }
                    }

                    if(sizeof($xids) >= 12 ) { break; }
                }

                if(!empty($xids)) {
                    $xrows = $postDao->getOnSearchIds($xids);
                }

            }

            if(sizeof($xrows) < 20 ) {

                $limit = 20 - (sizeof($xrows)) ;
                $searchToken = (Util::tryEmpty($group_slug)) ? $itemObj->title : $group_slug.$itemObj->title ;
                $sphinx = new \com\indigloo\sc\search\SphinxQL();
                //@todo - number of hits based on number of words in token
                $searchIds = $sphinx->getRelatedPosts($searchToken,3,0,$limit);

                if(!empty($searchIds)) {

                    foreach($searchIds as $searchId){
                        if(!in_array($searchId,$xids) && ($searchId != $postId)) {
                            array_push($xids,$searchId);
                        }
                    }

                    $search_rows = $postDao->getOnSearchIds($searchIds);
                    $xrows = array_merge($xrows,$search_rows);
                }
            }


            if(sizeof($xrows) < 20 ) {
                //how many?
                $limit = 20 - (sizeof($xrows)) ;

                //find posts from same category
                $catCode = $postDBRow["cat_code"];

                if(!Util::tryEmpty($catCode)) {

                    $catRows = $postDao->getLatestOnCategory($catCode,$limit);
                    foreach($catRows as $catRow) {
                        if(!in_array($catRow["id"],$xids) && ($catRow["id"] != $postId)) {
                            array_push($xrows,$catRow);
                            array_push($xids,$catRow["id"]);
                        }
                    }
                }
            }


            $siteDao = new \com\indigloo\sc\dao\Site();
            $siteDBRow = $siteDao->getOnPostId($postId);

            $loginUrl = "/user/login.php?q=".Url::current();
            $formErrors = FormMessage::render();

            $pageTitle = $itemObj->title;
            $metaDescription = Util::abbreviate($postDBRow["description"],160);
            $metaKeywords = SeoData::getMetaKeywords($group_names);
            $pageUrl = Url::base().Url::current() ;
            
            $file = APP_WEB_DIR. '/view/item.php' ;
            include($file);
        }
    }
}
?>
