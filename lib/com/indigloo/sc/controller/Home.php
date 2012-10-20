<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;
    use \com\indigloo\sc\auth\Login as Login ;

    class Home {

        private $ids ;
        private $homeDBRows ;

        function __construct() {
            $this->ids = array();
            $this->homeDBRows = array();
        }

        function combine($row) {
            if(!in_array($row['id'],$this->ids)) {
                array_push($this->homeDBRows,$row);
                array_push($this->ids,$row['id']);
            }
        }

        function process($params,$options) {
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;
            if($gpage == "1") {
                $this->loadHomePage();
            } else {
                $this->loadNextPage($gpage);
            }

        }

        private function getFeaturedPosts($postDao) {
            //33 featured posts
            $filters = array();
            $model = new \com\indigloo\sc\model\Post();
            $filter = new Filter($model);
            $filter->add($model::FEATURED,Filter::EQ,TRUE);
            array_push($filters,$filter);
            $rows = $postDao->getPosts(33,$filters);
            return $rows ;
        }

        private function loadHomePage() {

            $postDao = new \com\indigloo\sc\dao\Post();
            $featuredDBRows = $this->getFeaturedPosts($postDao);
            $userDBRows = array();

            // Do we have a login session?
            $loginId = Login::tryLoginIdInSession();
            if($loginId != null ) {
                $userDBRows = $postDao->getOnLoginId($loginId,4);
            }

            // how many are still missing?
            $pageSize = Config::getInstance()->get_value("main.page.items");
            //rest are random rows.
            $short = $pageSize - (sizeof($featuredDBRows) + sizeof($userDBRows)) ; 
            //20 latest posts
            $latestDBRows = $postDao->getLatest($short);

            $bucket = array_merge($userDBRows,$featuredDBRows);
            shuffle($bucket);
            $count = sizeof($bucket);

            for($i = 0 ; $i < $count ; $i++){
                $this->combine($bucket[$i]);
            }

            for($i = $count; $i < sizeof($latestDBRows) ; $i++){
                $this->combine($latestDBRows[$i]);
            }

            $endId = NULL ;
            if(sizeof($latestDBRows) > 0 ) {
                $endId =   $latestDBRows[sizeof($latestDBRows)-1]['id'] ;
            }

            $endId = base_convert($endId,10,36);
            $nparams = array('gpa' => $endId, 'gpage' => 2) ;
            $nextPageUrl = Url::addQueryParameters("/",$nparams);

            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            $file = APP_WEB_DIR. '/home.php' ;
            include ($file);
        }

        function loadNextPage($gpage) {

            $postDao = new \com\indigloo\sc\dao\Post();
            $total = $postDao->getTotalCount();
            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);

            $postDBRows = $postDao->getPaged($paginator);

            $pageHeader = '';
            $pageBaseUrl = '/' ;

            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            $file = APP_WEB_DIR. '/view/tiles-page.php' ;
            include ($file);
            
        }


    }
}
?>
