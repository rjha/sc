<?php

    $s_time = microtime(true);
    include 'sc-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger  as Logger ;
    use \com\indigloo\Util  as Util ;


    $router = new com\indigloo\sc\router\Router();
    $originalURI = $_SERVER['REQUEST_URI'];
    $requestURI = $originalURI ;

    //initialize routing table
    $router->initTable();
    $pos = strpos($originalURI, '?');
    $qpart = NULL ;

    if($pos !== false) {
        //remove the part after ? from Url
        // routing does not depends on query parameters
        $requestURI = substr($originalURI,0,$pos);
        $qpart = substr($originalURI, $pos+1);
    }

    $route = $router->getRoute($requestURI);

    if(is_null($route)) {
        //No valid route for this path
        $message = sprintf("No route for path %s",$requestURI);
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
            $message = sprintf("controller %s :: path is %s  ", $controllerName, $requestURI);
            Logger::getInstance()->debug($message);
            Logger::getInstance()->dump($route);
        }

        $controller = new $controllerName();
        $controller->process($route["params"], $route["options"]);

    }

    $e_time = microtime(true);
    printf("Request %s took %f microseconds \n", $originalURI, ($e_time - $s_time)*1000);

?>
