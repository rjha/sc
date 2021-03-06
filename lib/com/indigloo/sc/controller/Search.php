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

            if(empty($token)) {
                header("Location: / ");
            }

            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $sphinx = new \com\indigloo\sc\search\SphinxQL();
           
            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$pageSize);

            $ids = $sphinx->getPagedPosts($token,$paginator);
            
            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$token" ;
                $pageBaseUrl = "/search/site";

                $template = APP_WEB_DIR. '/view/search.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "No results" ;
                $template = APP_WEB_DIR. '/view/notiles.php';

            }

            $groupIds = $sphinx->getGroups($token,0,25);
            $groupDao = new \com\indigloo\sc\dao\Group();
            $groupDBRows = $groupDao->getOnSearchIds($groupIds);
             
            $sphinx->close();
            
            $pageTitle = SeoData::getPageTitleWithNumber($gpage,$token);
            $metaKeywords = SeoData::getMetaKeywords($token);
            $metaDescription = SeoData::getMetaDescriptionWithNumber($gpage,$token);

            include($template);
        }
    }
}
?>
