<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;


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
            $gpage = Util::tryArrayKey($params,"gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;
            
            if($gpage == "1") {
                $this->loadHomeRows();
            } else {
                $this->loadNextRows($gpage);
            }

            //$this->homeDBRows have been loaded.
            $nextPageUrl = "/home/page/".($gpage + 1) ;
            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            $file = APP_WEB_DIR. '/home.php' ;
            include ($file);


        }


        private function loadHomeRows() {

            $postDao = new \com\indigloo\sc\dao\Post();
            $randomDBRows = array();

            //10 featured posts
            $filters = array();
            $model = new \com\indigloo\sc\model\Post();
            $filter = new Filter($model);
            $filter->add($model::FEATURED,Filter::EQ,TRUE);
            array_push($filters,$filter);
            $featureDBRows = $postDao->getPosts(10,$filters);

            //20 latest posts
            $latestDBRows = $postDao->getLatest(0,20);
            $pageSize = Config::getInstance()->get_value("main.page.items");
            //rest are random rows.
            $short = $pageSize - (sizeof($featureDBRows) + sizeof($latestDBRows)) ;
            if($short > 0 ) {
                //pull random rows
                $randomDBRows = $postDao->getRandom($short);
            }

            $bucket = array_merge($featureDBRows,$randomDBRows);
            $count = sizeof($bucket);

            for($i = 0 ; $i < $count ; $i++){
                $this->combine($latestDBRows[$i]);
                $this->combine($bucket[$i]);
            }

            for($i = $count; $i < sizeof($latestDBRows) ; $i++){
                $this->combine($latestDBRows[$i]);
            }


        }

         function loadNextRows($gpage) {

            $postDao = new \com\indigloo\sc\dao\Post();
            $pageSize = Config::getInstance()->get_value("main.page.items");
            $offset = ($pageSize*$gpage )  - 1 ;
            $limit = $pageSize ;
            $this->homeDBRows = $postDao->getLatest($offset,$limit);

        }


    }
}
?>
