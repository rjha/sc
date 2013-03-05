<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;
  
    
    class ItemsMap {

        function __construct() {

        }

        function process($params,$options) {
            $postDao = new \com\indigloo\sc\dao\Post();
            
            $qparams = Url::getRequestQueryParams();
            $gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;

            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);    

            $postDBRows = $postDao->getPaged($paginator);

            $pageHeader = '';
            $pageBaseUrl = $options["path"];

            $pageTitle = SeoData::getPageTitleWithNumber($gpage,"recent items");
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getMetaDescriptionWithNumber($gpage,"recent items");

            $file = APP_WEB_DIR. '/view/tiles-page.php' ;
            include ($file);

        }

    }
}
?>
