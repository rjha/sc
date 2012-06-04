<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\html\Seo as SeoData ;



    class Site {

        function process($params,$options) {

            if(is_null($params) || empty($params)) {
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $siteId = Util::tryArrayKey($params,"site_id");
            $cname = Url::tryQueryParam("cname");

            if(empty($siteId) || empty($cname)) {
                $controller = new \com\indigloo\sc\controller\Http400();
                $controller->process();
                exit;
            }

            $siteDao = new \com\indigloo\sc\dao\Site();
            $postDBRows = $siteDao->getPostsOnId($siteId,50);
            
            $pageHeader = $cname ;
            $pageTitle = SeoData::getHomePageTitle();
            $metaDescription = SeoData::getHomeMetaDescription();
            $metaKeywords = SeoData::getHomeMetaKeywords();

            $file = APP_WEB_DIR. '/view/tiles.php' ;
            include ($file);
        }
    }
}
?>
