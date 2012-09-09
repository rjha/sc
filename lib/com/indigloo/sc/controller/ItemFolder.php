<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;
  
    
    class ItemFolder {

        function __construct() {

        }

        function process($params,$options) {
            $postDao = new \com\indigloo\sc\dao\Post();
            $total = $postDao->getTotalCount();

            $qparams = Url::getRequestQueryParams();
            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);    

            $postDBRows = $postDao->getPaged($paginator);

            $pageHeader = '';
            $pageBaseUrl = $options["path"];

            $pageTitle = SeoData::getHomePageTitle();
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            $file = APP_WEB_DIR. '/view/tiles-page.php' ;
            include ($file);

        }

    }
}
?>
