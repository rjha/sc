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
            $limit = Config::getInstance()->get_value("search.page.items");
            
            // fetch top N rows from sc_post that match our filter
            // this relies on the default sorting in mysql#Posts::getPosts() method
            
            $postDBRows = $postDao->getPosts($limit,$filters);

            $pageHeader = 'Editor\'s Pick';
            $pageTitle = "items on 3mik selected by our editors ";
            $metaDescription = SeoData::getHomeMetaDescription();
            $metaKeywords = SeoData::getHomeMetaKeywords();

            $view = APP_WEB_DIR. '/view/tiles.php' ;
            include($view); 
        }
    }
}
?>
