<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\util\PseudoId as PseudoId ;
  
	
    class User {
        
        function process($params,$options) {
            
            if(is_null($params) || empty($params))
                trigger_error("Required params is null or empty", E_USER_ERROR);

			$pubUserId = Util::getArrayKey($params,"login_id");
            $loginId = PseudoId::decode($pubUserId);
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);

            if(empty($userDBRow)) {
                //not found
                $controller = new \com\indigloo\sc\controller\Http404();
                $controller->process();
                exit;
            }

            $userName = $userDBRow['name'];

            $postDao = new \com\indigloo\sc\dao\Post() ;
            $filter = array($postDao::LOGIN_ID_COLUMN => $loginId);
            $total = $postDao->getTotalCount($filter);

            $pageSize =	Config::getInstance()->get_value("user.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
            $postDBRows = $postDao->getPaged($paginator,$filter);

            $template = $_SERVER['APP_WEB_DIR']. '/view/tiles-page.php';
            //page variables
            $pageTitle = "public profile of ".$userName;
            $pageHeader = "Posts by ".$userName;
            $pageBaseUrl = "/pub/user/".$pubUserId ;

            include($template); 

        }
    }
}
?>
