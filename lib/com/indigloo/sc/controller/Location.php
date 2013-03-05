<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\sc\html\Seo as SeoData ;


    class Location {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            // our router discards the query part from a URL so the
            // routing works with the query part as well (like /router/url?q1=x&q2=y
            $token = Util::getArrayKey($params,"location");
            if(is_null($token)) {
                header("Location: / ");
            }

            //search sphinx index
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            
            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$pageSize);
            $ids = $sphinx->getPagedPosts($token,$paginator);
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$token" ;
                $pageBaseUrl = "/search/location/$token";

                $template = APP_WEB_DIR. '/view/tiles-page.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "No Results" ;
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
