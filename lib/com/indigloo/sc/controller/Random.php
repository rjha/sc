<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
  
    
    class Random {
        
        function process($params,$options) {
            
            $postDao = new \com\indigloo\sc\dao\Post();
            $total = $postDao->getTotalCount();
            $rows1 = $postDao->getRandom(25);

            $ids = array();
            for($i = 1; $i <= 25;$i++) {
                $ids[] = mt_rand(1,$total-1);
            }

            $rows2 = $postDao->getOnSearchIds($ids);
            $postDBRows = array_merge($rows1,$rows2);
            
            $pageHeader = '<a href="/surprise/me">Try again?</a>';
            $pageTitle = SeoData::getHomePageTitle(); 
            $metaDescription = SeoData::getHomeMetaDescription();
            $metaKeywords = SeoData::getHomeMetaKeywords();

            $file = APP_WEB_DIR. '/view/tiles.php' ;
            include ($file);
        
        }


    }
}
?>
