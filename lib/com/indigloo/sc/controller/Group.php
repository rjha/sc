<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\ui\Pagination as Pagination ;
    use \com\indigloo\sc\html\Seo as SeoData ;

    class Group {

        function process($params,$options) {

            if(is_null($params) || empty($params)){
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $token = Util::getArrayKey($params,"name");
            
            // group controller is invoked via the fixed links 
            // (as opposed to users typing in search box)
            // so we (exact) match this token against post_groups index.

            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $qparams = Url::getRequestQueryParams();
            
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$pageSize);

            $ids = $sphinx->getPagedPostByGroup($token,$paginator);
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;
            $groupName = \com\indigloo\util\StringUtil::convertKeyToName($token);

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$groupName" ;
                $pageBaseUrl = "/group/$token" ;
                $template = APP_WEB_DIR. '/view/tiles-page.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {

                $pageHeader = "No results" ;
                $template = APP_WEB_DIR. '/view/notiles.php';
            }

            $pageTitle = SeoData::getPageTitleWithNumber($gpage,$groupName);
            $metaKeywords = SeoData::getMetaKeywords($groupName);
            $metaDescription = SeoData::getMetaDescriptionWithNumber($gpage,$groupName);

            include($template);
        }
    }
}
?>
