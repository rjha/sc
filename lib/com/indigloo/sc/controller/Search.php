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
            $total = $sphinx->getPostsCount($token);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$total,$pageSize);

            $ids = $sphinx->getPagedPosts($token,$paginator);
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$token - search results" ;
                $pageBaseUrl = "/search/site";

                $template = APP_WEB_DIR. '/view/search.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {
                $pageHeader = "No results found for $token" ;
                $template = APP_WEB_DIR. '/view/notiles.php';

            }

            $groupDao = new \com\indigloo\sc\dao\Group();
            $groupDBRows = $groupDao->search($token,$pageSize);

            $pageTitle = SeoData::getPageTitle($token);
            $metaKeywords = SeoData::getMetaKeywords($token);
            $metaDescription = SeoData::getMetaDescription($token);

            include($template);
        }
    }
}
?>
