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

            $slug = Util::getArrayKey($params,"name");
            //break hyphenated tokens into normal words for sphinx
            //$token = \com\indigloo\util\StringUtil::convertKeyToName($slug);
            // group index settings - no prefix,charset_type sbcs, ignore_chars U+002D
            $token = $slug;

            //get match on group slug
            $sphinx = new \com\indigloo\sc\search\SphinxQL();
            $total = $sphinx->getGroupsCount($token);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$total,$pageSize);

            $ids = $sphinx->getPagedGroups($token,$paginator);
            $sphinx->close();

            $template =  NULL ;
            $searchTitle = NULL ;
            $groupName = \com\indigloo\util\StringUtil::convertKeyToName($token);

            if(sizeof($ids) > 0 ) {
                $pageHeader = "$groupName -  $total results" ;
                $pageBaseUrl = "/group/$slug" ;
                $template = APP_WEB_DIR. '/view/tiles-page.php';
                $postDao = new \com\indigloo\sc\dao\Post();
                $postDBRows = $postDao->getOnSearchIds($ids) ;

            } else {

                $pageHeader = "No results found for group $groupName" ;
                $template = APP_WEB_DIR. '/view/notiles.php';
            }

            $pageTitle = SeoData::getPageTitle($groupName);
            $metaKeywords = SeoData::getMetaKeywords($groupName);
            $metaDescription = SeoData::getMetaDescription($groupName);

            include($template);
        }
    }
}
?>
