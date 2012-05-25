<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\sc\html\Seo as SeoData ;
  
    
    class Location {
        
        function process($params,$options) {
            
            if(is_null($params) || empty($params))
                trigger_error("Required params is null or empty", E_USER_ERROR);

            // our router discards the query part from a URL so the 
            // routing works with the query part as well (like /router/url?q1=x&q2=y
            $token = Util::getArrayKey($params,"location");
            if(is_null($token)) {
                header("Location: / ");
            }

            //search sphinx index
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
                $pageHeader = "$token - $total results" ;
                $pageBaseUrl = "/search/location/$token";

                $template = APP_WEB_DIR. '/view/tiles-page.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "No Results for $token" ;
                $template = APP_WEB_DIR. '/view/notiles.php';

            }

            $pageTitle = SeoData::getPageTitle($token);
            $metaKeywords = SeoData::getMetaKeywords($token);
            $metaDescription = SeoData::getMetaDescription($token);

            include($template); 
        }
    }
}
?>
