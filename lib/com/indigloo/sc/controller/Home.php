<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class Home {

        private $ids ;
        private $homeDBRows ;

        function __construct() {
            $this->ids = array();
            $this->homeDBRows = array();
        }

        function combine($item,$key) {
            if(!in_array($item['id'],$this->ids)) {
                array_push($this->homeDBRows,$item);
            }
        }
      
        function process($params,$options) {
            $gpage = Url::tryQueryParam("gpage");
            if(is_null($gpage) || ($gpage == '1')) {
                $this->processHome($params,$options);
            } else {
                $this->processNext($params,$options);
            }  
        }

        function processNext($params,$options) {
            $postDao = new \com\indigloo\sc\dao\Post();
			$total = $postDao->getTotalCount();

			$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
			$pageSize =	Config::getInstance()->get_value("main.page.items");
			$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	

			$postDBRows = $postDao->getPaged($paginator);

            $pageHeader = '';
            $pageBaseUrl = '/' ;

            $file = $_SERVER['APP_WEB_DIR']. '/view/tiles-page.php' ;
            include ($file);
 

        }

        function processHome($params,$options) {

            //get featured items

            $postDao = new \com\indigloo\sc\dao\Post();
			$total = $postDao->getTotalCount();

            $filter = array($postDao::FEATURE_COLUMN => 1);
            $featureDBRows = $postDao->getPosts($filter,25);

            $postDBRows = array();
            $randomDBRows = array();

            $latestDBRows = $postDao->getLatest(45);
            //shortfall?
            $short = 75 - (sizeof($featureDBRows) + sizeof($latestDBRows)) ;
            if($short > 0 ) {
                //pull random rows
                $randomDBRows = $postDao->getRandom($short);
            }
            
            array_walk($featureDBRows,array($this,"combine"));
            array_walk($latestDBRows,array($this,"combine"));
            array_walk($randomDBRows,array($this,"combine"));

            $endId = NULL ;
            if(sizeof($latestDBRows) > 0 ) {
                $endId =   $latestDBRows[sizeof($latestDBRows)-1]['id'] ;
            }

            $endId = base_convert($endId,10,36);
            $nparams = array('gpa' => $endId, 'gpage' => 2) ;
            $nextPageUrl = Url::addQueryParameters("/",$nparams);

            $file = $_SERVER['APP_WEB_DIR']. '/home.php' ;
            include ($file);
 
        }

    }
}
?>
