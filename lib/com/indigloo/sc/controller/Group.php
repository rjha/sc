<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
    use \com\indigloo\ui\Pagination as Pagination ;
  
	
    class Group {
        
        function process($params,$options) {
            
            if(is_null($params) || empty($params))
                trigger_error("Required params is null or empty", E_USER_ERROR);

			$slug = Util::getArrayKey($params,"name");
            //break hyphenated tokens into normal words for sphinx
            //$token = \com\indigloo\util\StringUtil::convertKeyToName($slug);
            // group index settings - no prefix,charset_type sbcs, ignore_chars U+002D 
            $token = $slug;

            //get match on group slug
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $total = $sphinx->getGroupsCount($token);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
            $pageSize =	50;
            $paginator = new Pagination($qparams,$total,$pageSize);	

            $ids = $sphinx->getPagedGroups($token,$paginator);   
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $pageHeader = "About $total results for $token" ;
                $pageBaseUrl = "/group/$slug" ;
                $template = $_SERVER['APP_WEB_DIR']. '/view/tiles.php';
                $questionDao = new \com\indigloo\sc\dao\Question();
                $questionDBRows = $questionDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "No Results for group $token" ;
                $template = $_SERVER['APP_WEB_DIR']. '/view/notiles.php';

            }


            include($template); 
        }
    }
}
?>
