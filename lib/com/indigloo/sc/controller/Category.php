<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Constants as Constants;
	
    class Category {
        
        function process($params,$options) {
            $categoryId = Util::getArrayKey($params,'category_id');

            /*
            $postDao = new \com\indigloo\sc\dao\Post();
            $filter = array($postDao::FEATURE_COLUMN => 1);
            $postDBRows = $postDao->getPosts($filter,50);
            $pageHeader = 'Editor\'s Pick';
            $view = $_SERVER['APP_WEB_DIR']. '/view/tiles.php' ;
            include($view); 
             */
        }
    }
}
?>
