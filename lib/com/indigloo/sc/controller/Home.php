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

        private function getFeaturedPosts($postDao,$limit) {
            //33 featured posts
            $filters = array();
            $model = new \com\indigloo\sc\model\Post();
            $filter = new Filter($model);
            $filter->add($model::FEATURED,Filter::EQ,TRUE);
            array_push($filters,$filter);
            $rows = $postDao->getPosts($limit,$filters);
            return $rows ;
        }

        /*
         * Home page mixing
         * 
         * limit to - 04 user posts - To cover user session
         * limit to - 23 featured posts 
         * minimum 10 latest posts 
         * - To cover global feeds and latest arrivals
         * - still not pageSize?
         * - fetch more latest DB rows
         *
         */
        private function loadHomePage() {
            $pageSize = Config::getInstance()->get_value("main.page.items");
            $postDao = new \com\indigloo\sc\dao\Post();
            $fp_size = $pageSize - 14 ;
            $fp_size = ($fp_size <= 4 ) ? 4 : $fp_size ; 
            
            $featuredDBRows = $this->getFeaturedPosts($postDao,$fp_size);
            $userDBRows = array();

            // Do we have a login session? 4 user posts
            $loginId = Login::tryLoginIdInSession();
            if($loginId != null ) {
                $userDBRows = $postDao->getOnLoginId($loginId,4);
            }

            $short = $pageSize - (sizeof($featuredDBRows) + sizeof($userDBRows)) ; 
            // if page size is less than feature + user DB rows 
            // even then we need to fetch few latest DB rows to make the 
            // pagination right.
            $short = ($short <= 4) ? 4 : $short ;
            // atleast 4 latest items, at max page size of latest items
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
            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);

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
