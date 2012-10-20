<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\util\Nest as Nest;
    use \com\indigloo\ui\Pagination as Pagination;
    use \com\indigloo\sc\html\Seo as SeoData ;

    class Category {

        function process($params,$options) {

            $seoKey = Util::getArrayKey($params,"category_id");
            $collectionDao = new \com\indigloo\sc\dao\Collection();
            $zmember = $collectionDao->uizmemberOnSeoKey(Nest::ui_category(),$seoKey);

            if(is_null($zmember) || !isset($zmember["ui_code"])) {
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            $code = $zmember["ui_code"];
            $catName = $zmember["name"];

            $postDao = new \com\indigloo\sc\dao\Post();
            $total = $postDao->getTotalOnCategory($code);

            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("search.page.items");
            $paginator = new Pagination($qparams,$total,$pageSize);
            $postDBRows = $postDao->getPagedOnCategory($paginator,$code);
            
            $pageHeader = $catName;

            $pageBaseUrl = "/category/$seoKey";
            $pageTitle = SeoData::getPageTitle($catName);
            $metaKeywords = SeoData::getMetaKeywords($catName);
            $metaDescription = SeoData::getMetaDescription($catName);

            $file = APP_WEB_DIR. '/view/tiles-page.php' ;
            include ($file);
        }
    }
}
?>
