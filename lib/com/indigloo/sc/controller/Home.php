<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class Home {
        
        function process($params,$options) {
            
            //following variables will be visible in $file as well
			$questionDao = new \com\indigloo\sc\dao\Question();
			$total = $questionDao->getTotalCount();

			$qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);
			$pageSize =	Config::getInstance()->get_value("main.page.items");
			$paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	

			$questionDBRows = $questionDao->getPaged($paginator);

            $file = $_SERVER['APP_WEB_DIR']. '/home.php' ;

            include ($file);
		
        }
    }
}
?>
