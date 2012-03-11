<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class Search {
        
        function process($params,$options) {
            
            $token = Url::tryQueryParam("gt");
            if(is_null($token)) {
                header("Location: / ");
            }

            //search sphinx index
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $ids = $sphinx->getPostIds($token);

            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $searchTitle = "Results for $token" ;
                $template = $_SERVER['APP_WEB_DIR']. '/search/results.php';
                $questionDao = new \com\indigloo\sc\dao\Question();
                $questionDBRows = $questionDao->getOnSearchIds($ids) ;

            } else {
                $searchTitle = "No Results for $token" ;
                $template = $_SERVER['APP_WEB_DIR']. '/search/noresult.php';

            }

            include($template); 
        }
    }
}
?>
