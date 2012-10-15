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

            $xids = array($postId);
            $xrows = array();
            $limit = 10 ;

            /* site metadata and posts */ 
            $siteDao = new \com\indigloo\sc\dao\Site();
            $siteMetaRow = $siteDao->getOnPostId($postId);
            $siteId = $siteMetaRow["id"];
            $site_rows = $siteDao->getPostsOnId($siteId,8);
            $sitePostRows = array();

            foreach($site_rows as $row) {
                if(!in_array($row["id"],$xids)) {
                    array_push($sitePostRows,$row);
                    array_push($xids,$row["id"]);
                    if(sizeof($sitePostRows) > 4 ) { break ;}
                }
            }

            /* related to item : via groups */
            $group_slug = $postDBRow["group_slug"];
            $groupDao = new \com\indigloo\sc\dao\Group();
            //@imp for display purpose only
            // convert tokens to names and join by comma
            $group_names = $groupDao->tokenizeSlug($group_slug,",",true);
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $searchToken = NULL ;
            
            /* 
             * recipe for fetching related posts
             * ------------------------------------------
             * 
             * 1) groups (tags) are priority #1 for matching
             * do exact match against sc_post.group_slug index 
             * (not polluted by other data like description etc.)
             * 
             * 2) Next try to do a "related items" match via quorum operator
             * use sc_post.title against posts index - # of hits is 3
             * 
             * 
             */

            if(!Util::tryEmpty($group_slug)) {
                $ids = $sphinx->getPostByGroup($group_slug,0,16);
                
                //unique ids?
                //@imp order matters for array_diff
                // bucket should be the second argument
                $ids = Util::fast_array_diff($ids,$xids);
                //xids for next iteration
                $xids = array_merge($xids,$ids);    

                if(!empty($ids)) {
                    $xrows = $postDao->getOnSearchIds($ids);
                }

            }

            if(sizeof($xrows) < 20 ) {
                
                $limit = 20 - (sizeof($xrows)) ;
                $limit = ($limit > 4 ) ? 4 : $limit ;

                $searchToken = $itemObj->title ;
                $sphinx = new \com\indigloo\sc\search\SphinxQL();
                //@todo - number of hits based on number of words in token
                $searchIds = $sphinx->getRelatedPosts($searchToken,3,0,$limit);
                //unique search ids?
                //@imp order matters for array_diff

                $searchIds = Util::fast_array_diff($searchIds,$xids);

                //xids for next iteration
                $xids = array_merge($searchIds,$xids);

                if(!empty($searchIds)) {
                    $search_rows = $postDao->getOnSearchIds($searchIds);
                    //collect in rows bucket
                    $xrows = array_merge($xrows,$search_rows);
                }
            }

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
