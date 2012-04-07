<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Constants as Constants;
    use \com\indigloo\sc\html\Seo as SeoData ;
	
    class Editor {
        
        function process($params,$options) {
            $postDao = new \com\indigloo\sc\dao\Post();
            $filter = array($postDao::FEATURE_COLUMN => 1);
            $postDBRows = $postDao->getPosts($filter,50);

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
