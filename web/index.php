<?php

    include 'sc-app.inc';
	include($_SERVER['APP_WEB_DIR'] . '/inc/header.inc');
	
	use \com\indigloo\Configuration as Config ;
	use \com\indigloo\Logger  as Logger ;
	use \com\indigloo\Util  as Util ;
	
     
    $router = new com\indigloo\sc\router\Router();
	
    //initialize routing table
    $router->initTable();
	$requestURI = $_SERVER['REQUEST_URI'];
	$pos = strpos($requestURI, '?');
	$qpart = NULL ;
 
	if($pos !== false) {
		$requestURI = substr($_SERVER['REQUEST_URI'],0,$pos);
		$qpart = substr($_SERVER['REQUEST_URI'], $pos+1);
	}
	
    $route = $router->getRoute($requestURI);
    
	
	if(is_null($route)) {
		//No valid route for this path
		$message = sprintf("No route for path %s",$_SERVER['REQUEST_URI']);
		Logger::getInstance()->error($message);
		
		$controller = new \com\indigloo\sc\controller\Http404();
		$controller->process();
		exit;

    } else {
		$controllerName = $route["action"];
		//add query part
		if(!is_null($qpart)){
			$route["params"]["q"] = $qpart ;
		}
		
		if(Config::getInstance()->is_debug()) {
			$message = sprintf("controller %s :: path %s  route is ", $controllerName, $_SERVER['REQUEST_URI']);
			Logger::getInstance()->debug($message);
			Logger::getInstance()->dump($route);
		}
		
		$controller = new $controllerName();
		$controller->process($route["params"], $route["options"]);
		
	
	}

?>
