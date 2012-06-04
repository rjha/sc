<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;


    class User {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }
            
            $pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);

            if(empty($userDBRow)) {
                //not found
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            //meta data about user - #groups/#posts/#comments/#followers etc.
            // user feeds
            $activityDao = new \com\indigloo\sc\dao\ActivityFeed();
            $userFeeds = $activityDao->getUser($loginId);

            $postDao = new \com\indigloo\sc\dao\Post() ;

            //create filter
            $model = new \com\indigloo\sc\model\Post();
            $filters = array();
            $filter = new Filter($model);
            $filter->add($model::LOGIN_ID,Filter::EQ,$loginId);
            array_push($filters,$filter);

            $total = $postDao->getTotalCount($filters);

            $pageSize = Config::getInstance()->get_value("user.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);
            $postDBRows = $postDao->getPaged($paginator,$filters);

            $template = APP_WEB_DIR. '/view/user/pub.php';

            //page variables
            $pageBaseUrl = "/pub/user/".$pubUserId ;
            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            include($template);

        }
    }
}
?>
