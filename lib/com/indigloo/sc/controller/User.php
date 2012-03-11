<?php
namespace com\indigloo\sc\controller{


	use \com\indigloo\Util as Util;
    use com\indigloo\Url;
	use \com\indigloo\Configuration as Config ;
  
	
    class User {
        
        function process($params,$options) {
            
            if(is_null($params) || empty($params))
                trigger_error("Required params is null or empty", E_USER_ERROR);

			$loginId = Util::getArrayKey($params,"login_id");
            $qparams = Url::getQueryParams($_SERVER['REQUEST_URI']);

            $userDao = new \com\indigloo\sc\dao\User();
            $userDBRow = $userDao->getOnLoginId($loginId);
            $userName = $userDBRow['name'];

            $questionDao = new \com\indigloo\sc\dao\Question() ;
            $filter = array($questionDao::LOGIN_ID_COLUMN => $loginId);
            $total = $questionDao->getTotalCount($filter);

            $pageSize =	Config::getInstance()->get_value("user.page.items");
            $paginator = new \com\indigloo\ui\Pagination($qparams,$total,$pageSize);	
            $questionDBRows = $questionDao->getPaged($paginator,$filter);

            $template = $_SERVER['APP_WEB_DIR']. '/view/tiles.php';
            //page variables
            $pageTitle = "public profile of ".$userName;
            $pageHeader = "Posts by ".$userName;
            $pageBaseUrl = "/pub/user/".$loginId ;

            include($template); 

        }
    }
}
?>
