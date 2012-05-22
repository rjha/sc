<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\html\Seo as SeoData ;
    use \com\indigloo\ui\Filter as Filter;
    
    class Editor {
        
        function process($params,$options) {
            $postDao = new \com\indigloo\sc\dao\Post();

            //post featured filter
            $filters = array();
            $model = new \com\indigloo\sc\model\Post();
            $filter = new Filter($model);
            $filter->add($model::FEATURED,Filter::EQ,TRUE);
            array_push($filters,$filter);
            $postDBRows = $postDao->getPosts(50,$filters);

            $pageHeader = 'Editor\'s Pick';
            $pageTitle = SeoData::getHomePageTitle(); 
            $metaDescription = SeoData::getHomeMetaDescription();
            $metaKeywords = SeoData::getHomeMetaKeywords();

            $view = $_SERVER['APP_WEB_DIR']. '/view/tiles.php' ;
            include($view); 
        }
    }
}
?>
