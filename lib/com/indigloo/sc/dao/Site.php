<?php

namespace com\indigloo\sc\dao {

    
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\sc\mysql as mysql;

    use \com\indigloo\sc\facebook\Router as FacebookRouter;
    use \com\indigloo\sc\facebook\Util as FacebookUtil;
    use \com\indigloo\sc\facebook\Graph as Graph;
    
    class Site {
        private $fbRouter;

        function __construct() {
            $this->fbRouter = new FacebookRouter();
            $this->fbRouter->initTable();
        }

        function process($postId) {
            trigger_error("Not implemented",E_USER_ERROR);
        }

        
        function processFBUrl($url,$path,$qpart) {
            if(empty($path)) {
                $path = "/" ;
            }

            $route = $this->fbRouter->getRoute($path);
            $page = array();

            if(!is_null($route)) {
                $action = $route['action'];
                $params = $route["params"];

                switch($action) {

                    case 'home' :
                        $page["canonicalUrl"] = $url ;
                        $page["hash"] = 1 ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;

                        break;
                    case 'page' :
                        $fbId = Util::getArrayKey($params, "id");
                        $page["canonicalUrl"] = $url ;
                        $page["hash"] = $fbId ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    case 'name' :
                        $token = Util::getArrayKey($params, "token");
                        $page["canonicalUrl"] = $url ;
                        $page["hash"] = Graph::getIdOnName($token) ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    case 'media' :
                        $qparams = Url::getQueryParams($url);
                        $set = $qparams["set"];
                        $fbId = FacebookUtil::getObjectIdInSet($set);
                        
                        //get object URL
                        $page["canonicalUrl"] = Graph::getLinkOnId($fbId);
                        $page["hash"] = $fbId ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break ;

                   case 'photo' :
                        $qparams = Url::getQueryParams($url);
                        $set = $qparams["set"];
                        $fbId = FacebookUtil::getObjectIdInSet($set);
                        //get object URL
                        $page["canonicalUrl"] = Graph::getLinkOnId($fbId) ;
                        $page["hash"] = $fbId ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    default:
                        break;
                }

            } else {
                $message = sprintf("Unknown Facebook url pattern [%s] \n",$url);
                Logger::getInstance()->error($message);
            }

            if(Config::getInstance()->is_debug()) {
                $message = sprintf("FB:: path is [%s] and query part is [%s] \n",$path,$qpart);
                Logger::getInstance()->debug($message);
                Logger::getInstance()->debug("Dump of router ::");
                Logger::getInstance()->dump($route);

            }

            return $page ;

        }

        function processUrl($url) {
            $scheme = \parse_url($url,PHP_URL_SCHEME);
            if(empty($scheme)) {
                $url = "http://".$url ;
            } 

            $info = \parse_url($url);

            if(Config::getInstance()->is_debug()) {
                $message = sprintf(" parse_url Dump for Url %s \n",$url);
                Logger::getInstance()->debug($message);
                Logger::getInstance()->dump($info);
            }

            $page = array();

            if($info["host"] == 'www.facebook.com' ) {
                $page = $this->processFBUrl($url,$info["path"],$info["query"]);

            } else {
                //canonical name
                $page["host"] = $info["host"];
                $page["hash"] = md5($info["host"]);
                $page["url"] = $url ;
                $page["canonicalUrl"] = "http://".$info["host"];
            }

            return $page ;

        }

    }

}
?>
