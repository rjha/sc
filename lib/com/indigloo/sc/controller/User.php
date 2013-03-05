<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;

    use \com\indigloo\sc\Constants as AppConstants;

    class User {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $gTab = Url::tryQueryParam("show");
            $gTab = empty($gTab) ? "index" : $gTab ;

            //routing based on tab
            switch($gTab) {
                case "index" :
                    $this->processIndex($params,$options);
                    break ;
                case "items" :
                    $this->processItems($params,$options);
                    break ;
                case "likes" :
                    $this->processLikes($params,$options);
                    break ;
                case "followers" :
                    $this->processGraph($params,$options,1);
                    break ;
                case "followings" :
                    $this->processGraph($params,$options,2);
                    break ;
                case "lists" :
                    $this->processLists($params,$options);
                    break ;

                default :
                    $this->processIndex($params,$options);
                    break ;
            }

            return ;
        }

        private function isValidUser($userDBRow) {
            if(empty($userDBRow)) {
                //not found
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }
        }

        private function processIndex($params,$options) {

            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getRequestQueryParams();
            $gNumDBRows = array();

            //data:1:user
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $this->isValidUser($userDBRow);

            //data:2:counters
            $analyticDao = new \com\indigloo\sc\dao\Analytic();
            $ucounters = $analyticDao->getUserCounters($loginId);
            

            //data:3:items
            $postDao = new \com\indigloo\sc\dao\Post() ;

            $model = new \com\indigloo\sc\model\Post();
            $filters = array();
            $filter = new Filter($model);
            $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
            array_push($filters,$filter);

            $postDBRows = $postDao->getLatest(8,$filters);
            $gNumDBRows["items"] = sizeof($postDBRows);

            //data:social graph
            $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
            $followers = $socialGraphDao->getFollowers($loginId,5);
            $followings = $socialGraphDao->getFollowing($loginId,5);

            $gNumDBRows["followers"] = sizeof($followers);
            $gNumDBRows["followings"] = sizeof($followings);

            $followerUIOptions = array(
                "ui" => "feed",
                "more" => "#");

            $followingUIOptions = array(
                "ui" => "feed",
                "more" => "#", "image" => false);

            //data:4:activity
            $activityDao = new \com\indigloo\sc\dao\Activity();
            $feedDataObj = $activityDao->getUserActivities($loginId,20);
            
            // data:5:likes
            $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();

            $model = new \com\indigloo\sc\model\Bookmark();
            $filters = array();

            $filter = new Filter($model);
            $filter->add($model::SUBJECT_ID_COLUMN,Filter::EQ,$loginId);
            array_push($filters,$filter);

            $filter = new Filter($model);
            $filter->add($model::VERB_COLUMN,Filter::EQ,AppConstants::LIKE_VERB);
            array_push($filters,$filter);

            $likeDBRows = $bookmarkDao->getLatest(8,$filters);
            $gNumDBRows["likes"] = sizeof($likeDBRows);

            //data:6:lists
            $listDao = new \com\indigloo\sc\dao\Lists();
            $listDBRows = $listDao->getLatestOnLoginId($loginId,4);
            $gNumDBRows["lists"] = sizeof($listDBRows);


            $template = APP_WEB_DIR. '/view/user/pub.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }

        private function processItems($params,$options){

            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getRequestQueryParams();
            
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $this->isValidUser($userDBRow);
           
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $postDao = new \com\indigloo\sc\dao\Post() ;

            //create filter
            $model = new \com\indigloo\sc\model\Post();
            $filters = array();
            $filter = new Filter($model);
            $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
            array_push($filters,$filter);

            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $postDBRows = $postDao->getPaged($paginator,$filters);

            $template = APP_WEB_DIR. '/view/user/items.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = sprintf("items by %s",$userDBRow["name"]);
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }

        private function processLikes($params,$options){

            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getRequestQueryParams();
            
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $this->isValidUser($userDBRow);
           
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $bookmarkDao = new \com\indigloo\sc\dao\Bookmark();

            //add login_id and code filters
            $model = new \com\indigloo\sc\model\Bookmark();
            $filters = array();

            //filter-1
            $filter = new Filter($model);
            $filter->add($model::SUBJECT_ID_COLUMN,Filter::EQ,$loginId);
            array_push($filters,$filter);

            //filter-2
            $filter = new Filter($model);
            $filter->add($model::VERB_COLUMN,Filter::EQ,AppConstants::LIKE_VERB);
            array_push($filters,$filter);

            $pageSize = Config::getInstance()->get_value("user.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $postDBRows = $bookmarkDao->getPaged($paginator,$filters);
            
            $template = APP_WEB_DIR. '/view/user/items.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = sprintf("Likes by %s",$userDBRow["name"]);
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }

        private function processGraph($params,$options,$source){

            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getRequestQueryParams();
            
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $this->isValidUser($userDBRow);
           
            $socialGraphDao = new \com\indigloo\sc\dao\SocialGraph();
            settype($source, "integer");
            
            $graphDBRows = array();
            $graphName = NULL ;

            switch($source) {
                case 1 :
                    $graphDBRows = $socialGraphDao->getFollowers($loginId,50);
                    $graphName = "followers" ;
                    break ;
                case 2 :
                    $graphDBRows = $socialGraphDao->getFollowing($loginId,50);
                    $graphName = "followings" ;
                    break ;
                default :
                    trigger_error("unknown source for social graph", E_USER_ERROR);
            }

            $template = APP_WEB_DIR. '/view/user/graph.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = sprintf("%s of %s",$graphName,$userDBRow["name"]);
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }

        private function processLists($params,$options){

            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getRequestQueryParams();
            
            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $this->isValidUser($userDBRow);
           
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $listDao = new \com\indigloo\sc\dao\Lists();

            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("user.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
            $listDBRows = $listDao->getPagedOnLoginId($paginator,$loginId);
                    
            $template = APP_WEB_DIR. '/view/user/lists.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = sprintf("All lists");
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }


    }
}
?>
