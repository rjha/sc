<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\Pagination as Pagination;
	
    class Category {
        
        function process($params,$options) {
            $categoryId = Util::getArrayKey($params,'category_id');
            $categoryDao = new \com\indigloo\sc\dao\Category();
            $code = $categoryDao->getCodeonId($categoryId);

           if(is_null($code)) {
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            $total = $categoryDao->getTotalCount($code);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
            $pageSize =	50;
            $paginator = new Pagination($qparams,$total,$pageSize);	
            $postDBRows = $categoryDao->getPaged($paginator,$code);
            $catName = $categoryDao->getName($code);

            if(sizeof($postDBRows) > 0 ) {
                $pageHeader = 'Category '.$catName;
            }else {
                $pageHeader = 'No Results for Category '.$catName;
            }

            $pageBaseUrl = "/category/$categoryId";

            $file = $_SERVER['APP_WEB_DIR']. '/view/tiles-page.php' ;
            include ($file);
        }
    }
}
?>