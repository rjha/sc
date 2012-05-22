<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\sc\html\Seo as SeoData ;
  
    
    class Search {
        
        function process($params,$options) {
            
            $token = Url::tryQueryParam("gt");
            if(is_null($token)) {
                header("Location: / ");
            }

            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $total = $sphinx->getPostsCount($token);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
            $pageSize = 50;
            $paginator = new Pagination($qparams,$total,$pageSize); 

            $ids = $sphinx->getPagedPosts($token,$paginator);            
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$token - $total Results" ;
                $pageBaseUrl = "/search/site";

                $template = $_SERVER['APP_WEB_DIR']. '/view/tiles-page.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "$token - No Results" ;
                $template = $_SERVER['APP_WEB_DIR']. '/view/notiles.php';

            }

            $pageTitle = SeoData::getPageTitle($token);
            $metaKeywords = SeoData::getMetaKeywords($token);
            $metaDescription = SeoData::getMetaDescription($token);

            include($template); 
        }
    }
}
?>
