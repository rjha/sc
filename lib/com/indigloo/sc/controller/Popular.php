<?php
namespace com\indigloo\sc\controller{


    use \com\indigloo\Util as Util;
    use com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\html\Seo as SeoData ;
    
    use \com\indigloo\sc\redis as redis ;
    use \com\indigloo\sc\util\Nest ;
    use \com\indigloo\sc\util\PseudoId ;

    
    class Popular {
        
        function process($params,$options) {
            
            $qparams = Url::getRequestQueryParams();
            $redis = new redis\Activity();

            $pageSize = Config::getInstance()->get_value("main.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams, $pageSize);
             
            $zsetKey = Nest::score("post", "likes");
            $members = $redis->getPagedZSet($zsetKey,$paginator);
            //first one is id, second one is score
            $count = 0 ;
            $scores = array();
            $ids = array();

            for($i = 1 ; $i < sizeof($members); $i++) {
                if($i % 2 == 0) {
                    array_push($scores,$members[$i-1]);
                }else {
                    $itemId = $members[$i-1];
                    $postId = PseudoId::decode($itemId);
                    array_push($ids,$postId);
                }

            }

            //get post rows using ids
            $postDao = new \com\indigloo\sc\dao\Post();
            $postDBRows = $postDao->getOnSearchIds($ids);
            
            $pageHeader = 'Most popular';
            $pageBaseUrl = '/pub/popular' ;

            $pageTitle =  "Most popular items on 3mik voted by users";
            $metaKeywords = SeoData::getHomeMetaKeywords();
            $metaDescription = SeoData::getHomeMetaDescription();

            $file = APP_WEB_DIR. '/view/tiles-page.php' ;
            include ($file);
        
        }


    }
}
?>
