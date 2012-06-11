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
        
        function getOnPostId($postId) {
            $row = mysql\Site::getOnPostId($postId);
            return $row ;
        }

        function getTotalPostsOnId($siteId) {
            $row = mysql\Site::getTotalPostsOnId($siteId);
            return $row['count'] ;
        }

        function getPostsOnId($siteId,$limit) {
            $rows = mysql\Site::getPostsOnId($siteId,$limit);
            return $rows ;
        }

        function getOnHash($hash) {
            $row = mysql\Site::getOnHash($hash);
            return $row ;
        }

        function getOrCreate($hash,$host,$canonicalUrl) {
            $hash = trim($hash);
            $siteId = NULL ;

            $row = $this->getOnHash($hash); 

            if(empty($row)){
                //create site 
                $siteId = mysql\Site::create($hash,$host,$canonicalUrl);
            } else {
                //found
                $siteId = $row['id'];
            }

            return $siteId ;

        }

        function process($postId) {
            //get links
            $postDao = new \com\indigloo\sc\dao\Post();
            $linkData = $postDao->getLinkDataOnId($postId);

            $links = $linkData["links"];
            if(is_null($links)) { return ; }
            $version = $linkData["version"];

            //clean tmp post-site table 
            mysql\Site::deleteTmpPSData($postId);

            foreach($links as $link) {
                $page = $this->processUrl($link);

                if(empty($page) 
                    || empty($page["canonicalUrl"]) 
                    || empty($page["hash"]) 
                    || empty($page["host"])) {

                    $message = "URL_PROC_ERROR :: [%s] of post [%d] is missing required data" ;
                    $message = sprintf($message,$link,$postId);
                    Logger::getInstance()->error($message);

                    //write to bad.url log file
                    $fhandle = NULL ;
                    $logfile = Config::getInstance()->get_value("bad.url.log");
                    if (!file_exists($logfile)) {
                        //create the file
                        $fhandle = fopen($logfile, "x+");
                    } else {
                        $fhandle = fopen($logfile, "a+");
                    }
                    fwrite($fhandle,$message);
                    fclose($fhandle);

                    // process next URL
                    // there are pros and cons of continuing on bad url vs. bailing out
                    // however URL processing is intensive job and it is better to examine
                    // bad URL in leisure because we may not have a solution for processing
                    // bad urls immediately
                    continue;

                }

                $siteId = $this->getOrCreate($page["hash"],$page["host"],$page["canonicalUrl"]);
                if(!is_null($siteId)){
                    mysql\Site::addTmpPSData($postId,$siteId);
                } else {
                    trigger_error("URL_PROC_ERROR :: Null site.id for $link", E_USER_ERROR);
                }
            }

            //Add new post+site data and update tracker
            mysql\Site::updateTracker($postId,$version);

        }

        function processFBUrl($url,$path) {
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
                        $page["hash"] = "FB1" ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;

                        break;
                    case 'page' :
                    case 'people': 
                        $fbId = Util::getArrayKey($params, "id");
                        $page["hash"] = empty($fbId) ? NULL : "FB".$fbId ;
                        $page["canonicalUrl"] = $url ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    case 'name' :
                        $token = Util::getArrayKey($params, "token");
                        $page["canonicalUrl"] = $url ;
                        $fbId =  Graph::getIdOnName($token) ;
                        $page["hash"] = empty($fbId) ? NULL : "FB".$fbId;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    case 'media' :
                    case 'photo' :
                        $qparams = Url::getQueryParams($url);
                        $set = $qparams["set"];
                        $fbId = FacebookUtil::getObjectIdInSet($set);
                        
                        //get object URL
                        $page["canonicalUrl"] = Graph::getLinkOnId($fbId);
                        $page["hash"] = empty($fbId) ? NULL : "FB".$fbId ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break ;

                    case 'profile' :
                        $qparams = Url::getQueryParams($url);
                        $fbId = $qparams["id"];
                        //get object URL
                        $page["canonicalUrl"] = Graph::getLinkOnId($fbId) ;
                        $page["hash"] = "FB".$fbId ;
                        $page["url"] = $url ;
                        $page["host"] = "www.facebook.com" ;
                        break;

                    case 'script' :
                        $message = sprintf("UNKNOWN_FB_SCRIPT :: [%s]",$url);
                        Logger::getInstance()->error($message);
                        break;


                    default:
                        break;
                }

            } else {
                $message = sprintf("UNKNOWN_FB_URL :: [%s]",$url);
                Logger::getInstance()->error($message);
            }

            if(Config::getInstance()->is_debug()) {
                $message = sprintf("FACEBOOK_URL ::  [%s] ",$url);
                Logger::getInstance()->debug($message);
                Logger::getInstance()->debug("Dump of router ::");
                Logger::getInstance()->dump($route);

            }

            return $page ;

        }

        function processUrl($url) {
            $page = array();

            //empty url
            if(empty($url)) {
                return $page ;
            }

            $scheme = \parse_url($url,PHP_URL_SCHEME);

            if(empty($scheme)) {
                $url = "http://".$url ;
            } 

            $info = \parse_url($url);
            //host check
            if(!isset($info["host"])) {
                $message = sprintf("BAD_SITE_URL :: host not found [ %s ] ",$url);
                Logger::getInstance()->error($message);
                return $page;
            }

            if(Config::getInstance()->is_debug()) {
                $message = sprintf("parse_url Dump for Url %s \n",$url);
                Logger::getInstance()->debug($message);
                Logger::getInstance()->dump($info);
            }

            if(strcasecmp($info["host"],'www.facebook.com') == 0 ) {
                $page = $this->processFBUrl($url,$info["path"]);

            } else {
                //canonical name
                $page["host"] = $info["host"];
                $page["hash"] = md5(strtolower($info["host"]));
                $page["url"] = $url ;
                $page["canonicalUrl"] = "http://".$info["host"];
            }

            return $page ;

        }

    }

}
?>
